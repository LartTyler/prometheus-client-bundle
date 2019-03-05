<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use DaybreakStudios\PrometheusClient\CollectorRegistry;
	use DaybreakStudios\PrometheusClient\CollectorRegistryInterface;
	use DaybreakStudios\PrometheusClientBundle\Listeners\MetricsEndpointListener;
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

			$registryId = isset($config['registry']) ? $config['registry'] : CollectorRegistry::class;

			if (!$container->hasDefinition($registryId)) {
				if (!class_exists($registryId) || !is_a($registryId, CollectorRegistryInterface::class, true)) {
					throw new \InvalidArgumentException(
						$registryId . ' must be a service ID, or a class implementing ' .
						CollectorRegistryInterface::class
					);
				}

				$registry = new Definition(
					$registryId,
					[
						new Reference($config['adapter']),
					]
				);

				$container->setDefinition($registryId, $registry);
			}

			$metricsConfig = isset($config['metrics']) ? $config['metrics'] : [];

			if (isset($metricsConfig['enabled']) ? $metricsConfig['enabled'] : true) {
				$metrics = new Definition(
					MetricsEndpointListener::class,
					[
						new Reference($registryId),
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