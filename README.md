## Getting Started
To add the bundle, run the following command.

```shell
$ composer require dbstudios/prometheus-client-bundle
```

If your project is using [Symfony Flex](https://symfony.com/doc/4.1/setup/flex.html), the bundle should be enabled
automatically. If not, you will need to add `DaybreakStudios\PrometheusClientBundle\DaybreakStudiosPrometheusClientBundle`
to your bundles file.

Next, if your `config` directory, you'll need to add the base configuration for the bundle.

```yaml
dbstudios_prometheus:
    adapter: Prometheus\Storage\APC
``` 

Where the base configuration should be placed will depend on your Symfony version. For 3.4 and older, this should be
in `app/config/config.yml`. For newer Symfony versions (4.0 and greater), this should be
`config/packages/dbstudios_prometheus.yaml`.