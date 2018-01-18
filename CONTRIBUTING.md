Contributing to the Domain Component
====================================

Thanks for getting interested in contributing to one of our projects. To help us the right way, here are some rules to follow.

Report bug
----------

To report a bug, just open a new issue. Try to:

- Be the more explicit as you can
- Be careful on naming your issue: try to be clear
- Use example, another project hosted on github may be an acceptable example
- Provide a unit test that exposes the bug

### Security issues

Don't open an issue but send an email to **maxime.veber@biig.fr**.

Contribute
----------

### Open new discussion

You may have a great idea. Do not hesitate to send it to us using the following rules:

1. Prefix your issue title by `[RFC]`
2. Describe your idea
3. Illustrate with fake code example what it could be

### Code Contribution

1. Start from the `master` branch
2. Verify that tests are green
3. Comment the code
5. Fix the codestyle of your code with the following command

```
php-cs-fixer.phar fix --config=.php_cs.dist
```

Release policy
--------------

The release policy of BiiG OSS was originaly defined [here](https://github.com/biig-io/DictionaryBundle/issues/12).

Here it is:

1. With every release we should fill the [release page](https://github.com/biig-io/DomainComponent/releases)
2. We should release maximum 1 week after some contribution is merged (except doc)
3. We open an issue/milestone for the release so we have a track and date for it
4. Every **contributor to the project & BiiGer** can make a release (others can ask for in a new issue)
5. We should apply this to any projects of BiiG
6. We must respect [semver](https://semver.org/)

It may not be apply correctly so you can open an issue to notice it.

License
-------

This library is under an MIT license. Any contribution you make on it is under the same MIT license.
