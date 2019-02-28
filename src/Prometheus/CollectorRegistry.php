<?php
	namespace DaybreakStudios\PrometheusClientBundle\Prometheus;

	use Prometheus\Collector;
	use Prometheus\Counter;
	use Prometheus\Gauge;
	use Prometheus\Histogram;
	use Prometheus\MetricFamilySamples;
	use Prometheus\Storage\Adapter;

	class CollectorRegistry {
		/**
		 * @var Adapter
		 */
		protected $adapter;

		/**
		 * @var Collector[]
		 */
		protected $collectors = [];

		/**
		 * CollectorRegistry constructor.
		 *
		 * @param Adapter $adapter
		 */
		public function __construct(Adapter $adapter) {
			$this->adapter = $adapter;
		}

		/**
		 * @return MetricFamilySamples[]
		 */
		public function collect() {
			return $this->adapter->collect();
		}

		/**
		 * @param Collector $collector
		 *
		 * @return $this
		 */
		public function register(Collector $collector) {
			$this->collectors[$collector->getName()] = $collector;

			return $this;
		}

		/**
		 * @param Collector|string $collector
		 *
		 * @return $this
		 */
		public function unregister($collector) {
			if (is_object($collector) && $collector instanceof Collector)
				$collector = $collector->getName();
			else if (!is_string($collector)) {
				throw new \InvalidArgumentException(
					'$collector must be the name of a collector or an instance of ' . Collector::class
				);
			}

			unset($this->collectors[$collector]);

			return $this;
		}

		/**
		 * @param string $name
		 * @param bool   $throwOnMissing
		 *
		 * @return Collector|null
		 */
		public function get($name, $throwOnMissing = true) {
			if (!$this->has($name)) {
				if ($throwOnMissing)
					throw new \RuntimeException('There is no registered collector named ' . $name);

				return null;
			}

			return $this->collectors[$name];
		}

		/**
		 * @param $name
		 *
		 * @return bool
		 */
		public function has($name) {
			return isset($this->collectors[$name]);
		}

		/**
		 * @param string $name
		 * @param bool   $throwOnMissing
		 *
		 * @return Collector|null
		 */
		public function getCounter($name, $throwOnMissing = true) {
			return $this->getOfType($name, Counter::class, $throwOnMissing);
		}

		/**
		 * @param string $name
		 * @param bool   $throwOnMissing
		 *
		 * @return Collector|null
		 */
		public function getGauge($name, $throwOnMissing = true) {
			return $this->getOfType($name, Gauge::class, $throwOnMissing);
		}

		/**
		 * @param string $name
		 * @param bool   $throwOnMissing
		 *
		 * @return Collector|null
		 */
		public function getHistogram($name, $throwOnMissing = true) {
			return $this->getOfType($name, Histogram::class, $throwOnMissing);
		}

		/**
		 * @param string $name
		 * @param string $class
		 * @param bool   $throwOnMissing
		 *
		 * @return Collector|null
		 */
		protected function getOfType($name, $class, $throwOnMissing = true) {
			$collector = $this->get($name, $throwOnMissing);

			if ($collector !== null && !is_a($collector, $class))
				throw new \Exception('Collector ' . $name . ' is not a ' . $class);

			return $collector;
		}
	}