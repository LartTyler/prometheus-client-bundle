<?php
	namespace DaybreakStudios\PrometheusClientBundle\DependencyInjection;

	use DaybreakStudios\PrometheusClient\CollectorRegistry;
	use Symfony\Component\Config\Definition\Builder\TreeBuilder;
	use Symfony\Component\Config\Definition\ConfigurationInterface;

	class Configuration implements ConfigurationInterface {
		/**
		 * {@inheritdoc}
		 */
		public function getConfigTreeBuilder() {
			$builder = new TreeBuilder();
			$root = $builder->root('dbstudios_prometheus');

			$root->children()
				->scalarNode('adapter')->end()
				->scalarNode('registry')->defaultValue(CollectorRegistry::class)->end()
				->arrayNode('metrics')->children()
				->booleanNode('enabled')->defaultTrue()->end()
				->scalarNode('path')->defaultValue('/metrics')->end()
				->end();

			return $builder;
		}
	}