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
    adapter: DaybreakStudios\PrometheusClient\Adapter\ApcuAdapter
``` 

Where the base configuration should be placed will depend on your Symfony version. For 3.4 and older, this should be
in `app/config/config.yml`. For newer Symfony versions (4.0 and greater), this should be
`config/packages/dbstudios_prometheus.yaml`.

The value of `adapter` must be the ID of a service extending `DaybreakStudios\PrometheusClient\Adapter\AdapterInterface`.
See the documentation for [`dbstudios/prometheus-client`](https://github.com/LartTyler/prometheus-client) for a list of
built-in adapters.

## Configuration
A complete, annotated configuration for this bundle may be found below.

```yaml
dbstudios_prometheus:
    # The ID of a service implementing DaybreakStudios\PrometheusClient\Adapter\AdapterInterface. No services are
    # registered by default, so you will need to choose an appropriate adapter for your environment, register it, then
    # reference it here.
    adapter: DaybreakStudios\PrometheusClient\Adapter\ApcuAdapter
    
    # The ID of a service implementing DaybreakStudios\PrometheusClient\CollectorRegistryInterface, OR the
    # fully-qualified class name of a class implementing the aforementioned interface. In the second case, the bundle
    # will automatically register the provided class as a service, using the class name as it's service ID.
    #
    # If you're using the built-in collector registry, you do not need to provide a value here.
    registry: DaybreakStudios\PrometheusClient\CollectorRegistry 
    
    # The metrics section contains options for the built-in metrics endpoint listener, which provides the information
    # Prometheus needs to scrape your application.
    metrics:
        # Whether or not the built-in metrics endpoint is enabled. If this option is false, you will need to implement
        # the metrics endpoint on your own (see the section titled "Custom Metrics Endpoint").
        enabled: true
        
        # The path to use for the metrics endpoint. If `metrics.enabled` is false, this option is ignored.
        path: /metrics
```

## Custom Metrics Endpoint
By default, this bundle registers a listener that responds to any requests to `dbstudios_prometheus.metrics.path` with
the metrics data that Prometheus will need to scrape from your application. If you'd like to implement this endpoint on
your own, see `DaybreakStudios\PrometheusClientBundle\Listeners\MetricsEndpointListener::onKernelRequest()` for an
example implementation.