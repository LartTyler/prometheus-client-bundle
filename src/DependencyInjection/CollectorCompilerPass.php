<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Reference;

	class CollectorCompilerPass implements CompilerPassInterface {
		/**
		 * {@inheritdoc}
		 */
		public function process(ContainerBuilder $container) {
			$registry = $container->findDefinition($container->getParameter('dbstudios_prometheus.registry'));
			$services = $container->findTaggedServiceIds('dbstudios_prometheus.collector');

			foreach ($services as $service => $_)
				$registry->addMethodCall('register', [new Reference($service)]);
		}
	}