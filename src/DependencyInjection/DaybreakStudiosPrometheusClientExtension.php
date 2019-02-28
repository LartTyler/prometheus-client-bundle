<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use DaybreakStudios\PrometheusClientBundle\Listeners\MetricsEndpointListener;
	use DaybreakStudios\PrometheusClientBundle\Prometheus\CollectorRegistry;
	use Prometheus\Storage\APC;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Definition;
	use Symfony\Component\DependencyInjection\Extension\Extension;
	use Symfony\Component\DependencyInjection\Reference;
	use Symfony\Component\HttpKernel\KernelEvents;

	class DaybreakStudiosPrometheusClientExtension extends Extension {
		/**
		 * {@inheritdoc}
		 */
		public function load(array $configs, ContainerBuilder $container) {
			$configuration = new Configuration();
			$config = $this->processConfiguration($configuration, $configs);

			if (!$container->hasDefinition(CollectorRegistry::class)) {
				$registry = new Definition(
					CollectorRegistry::class,
					[
						new Reference($config['adapter']),
					]
				);

				$container->setDefinition(CollectorRegistry::class, $registry);
			}

			$metricsConfig = isset($config['metrics']) ? $config['metrics'] : [];

			if (isset($metricsConfig['enabled']) ? $metricsConfig['enabled'] : true) {
				$metrics = new Definition(
					MetricsEndpointListener::class,
					[
						new Reference(CollectorRegistry::class),
						isset($metricsConfig['path']) ? $metricsConfig['path'] : '/metrics',
					]
				);

				$metrics->addTag(
					'kernel.event_listener',
					[
						'event' => KernelEvents::REQUEST,
						'method' => 'onKernelRequest',
						'priority' => '2048',
					]
				);

				$container->setDefinition(MetricsEndpointListener::class, $metrics);
			}
		}

		/**
		 * @return string
		 */
		public function getAlias() {
			return 'dbstudios_prometheus';
		}
	}