<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use DaybreakStudios\PrometheusClient\CollectorRegistryInterface;
	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Reference;

	class CollectorCompilerPass implements CompilerPassInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(ContainerBuilder $container) {
			$registry = $container->findDefinition(CollectorRegistryInterface::class);
			$services = $container->findTaggedServiceIds('dbstudios_prometheus.collector');

			foreach ($services as $service => $tags)
				$registry->addMethodCall('register', [new Reference($service)]);
		}
	}