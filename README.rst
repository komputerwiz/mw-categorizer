#####################
Wikipedia Categorizer
#####################

:Info: <http://kpsdecdzlilfrkgn.komputerwiz.net>

:Version: 0.1

:Date: Tue Nov 19 01:14:05 2013

:Authors:
    Matthew Barry <komputerwiz.matt@gmail.com>,
    Brett Walker <brett.walker2010@gmail.com>,
    Nicholas McHenry <rgtmchenry@gmail.com>

:Organization: Texas A&M University

:License: MIT


Deployment Instructions
=======================


Installing
----------

1.  Composer:

    .. code-block:: bash

        curl -s getcomposer.org/installer | php

2.  Client-side dependencies:

    .. code-block:: bash

        bower install

3.  Server-side dependencies:

    .. code-block:: bash

        php composer.phar install

4.  Compile generated/preprocessed assets:

    .. code-block:: bash

        php app/console --env=prod --no-debug assetic:dump


Updating
--------

1.  Pull changes:

    .. code-block:: bash

        git pull

2.  Update client-side dependencies:

    .. code-block:: bash

        bower install

3.  Update server-side dependencies:

    .. code-block:: bash

        php composer.phar install

4.  Clear the application cache

    .. code-block:: bash

        php app/console --env=prod --no-debug cache:clear

5.  Compile generated/preprocessed assets:

    .. code-block:: bash

        php app/console --env=prod --no-debug assetic:dump

6.  Deploy new bundle assets:

    .. code-block:: bash

        php app/console --env=prod --no-debug assets:install --symlink --relative web



