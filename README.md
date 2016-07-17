DirectAdmin PHP API
=======

A simple php class for logging in DirectAdmin panel and calling actions.
It can easily extended to what you need, currently I have added only some methods for one of my projects.

### Supported actions
1. `login`
2. `domain_pointer_add` (single domain)
3. `domain_pointer_delete` (single and multiple with comma separated domain names)
4. `file_put` (create new file or update existing file)
5. `file_get` (get contents of existing file)
