## Setup

1. `$ git clone git@github.com:Astoundify/wc-simple-registration.git && cd wc-simple-registration.git`

## Making Changes

`master` branch is always what is currently deployed on Astoundify, which means nothing should be committed directly to this branch.

### Fixing an Issue or Adding a Feature

1. All commits should relate to an existing issue on Github.
2. Create a new based off the current release branch related to the issue number. For example `issue/123`
3. Add your changes.
4. Open a Pull Request against the next release branch.
5. Assign at least one reviewer other than yourself to the Pull Request.
6. Once reviewed the reviewer can merge the feature in to the `release/x.x.x` branch.
