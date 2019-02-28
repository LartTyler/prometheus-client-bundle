<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use Prometheus\Storage\APC;
	use Symfony\Component\Config\Definition\Builder\TreeBuilder;
	use Symfony\Component\Config\Definition\ConfigurationInterface;
	use Symfony\Component\DependencyInjection\Reference;

	class Configuration implements ConfigurationInterface {
		/**
		 * {@inheritdoc}
		 */
		public function getConfigTreeBuilder() {
			$builder = new TreeBuilder();
			$root = $builder->root('dbstudios_prometheus');

			$root->children()
				->scalarNode('adapter')->defaultValue(new Reference(APC::class))->end()
				->arrayNode('metrics')->children()
					->booleanNode('enabled')->defaultTrue()->end()
					->scalarNode('path')->defaultValue('/metrics')->end()
				->end();

			return $builder;
		}
	}