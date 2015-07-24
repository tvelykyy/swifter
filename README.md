Swifter Documentation
========================

Snippet
----------------------------------
Snippet is a dynamic html. Snippet's html code is produced by invoking some service method and wrapping result of service
method execution with predefined html template.

Snippet consists of:
  * title
  * service
  * method
  * template_id
  * params

Sample configuration:
    {
        "title": "ALL_PAGES"
        "service": front.service.devtest
        "method": "getPages"
        "template_id": 1
        "params": {
            "offset": 0,
            "limit": 5
        }
    }

Each service in Symfony 2 has an alias. Suppose, we have service class Swifter\FrontBundle\Service\DevTestService with alias front.service.devtest.
Above snippet configuration tells us that Swifter\FrontBundle\Service\DevTestService#getPages($offset, $limit) will called
and results will be transformed into html by template with id=1.

To use this snippet on page block just put
    [[ALL_PAGES]]
and snippet would be resolved and replaced result html code.

Snippet parameters:
Property "params" defines default values of snippet parameters.
To override snippet default param values add query param with value to override

For example:
- to override offset param, use:
    http://your-site.com/page-with-examples-snippet.html?offset=5

- to override both params, use:
    http://your-site.com/page-with-examples-snippet.html?offset=5&limit=10

- if not query params provided, default will be used.
