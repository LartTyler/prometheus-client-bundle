<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use DaybreakStudios\PrometheusClient\CollectorRegistry;
	use DaybreakStudios\PrometheusClient\CollectorRegistryInterface;
	use DaybreakStudios\PrometheusClientBundle\Command\ClearCacheCommand;
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

			$registryId = $config['registry'] ?? CollectorRegistry::class;
			$container->setParameter('dbstudios_prometheus.registry', $registryId);

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
				$container->setAlias(CollectorRegistryInterface::class, $registryId);
			}

			$clearCommand = new Definition(
				ClearCacheCommand::class,
				[
					new Reference($config['adapter']),
				]
			);

			$clearCommand->addTag(
				'console.command',
				[
					'command' => 'dbstudios:prometheus:clear-cache',
				]
			);

			$container->setDefinition(ClearCacheCommand::class, $clearCommand);

			if ($config['metrics']['enabled'] ?? true) {
				$metrics = new Definition(
					MetricsEndpointListener::class,
					[
						new Reference($registryId),
						$config['metrics']['path'] ?? '/metrics',
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