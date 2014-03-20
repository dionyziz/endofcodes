# Contributing to End of Codes

End of Codes is an open source project. We love contributions. We would like to
work more bazaar-style and encourage external authors. Thanks for your interest
in contributing. You're amazing!

This document outlines some contribution guidelines so that your contributions
can be included in the codebase.

## Workflow
To contribute, follow these simple steps:

1. [Fork](https://help.github.com/articles/fork-a-repo) the GitHub repo.
1. [Clone your fork](https://help.github.com/articles/fork-a-repo#step-2-clone-your-fork).
1. [Configure remotes](https://help.github.com/articles/fork-a-repo#step-3-configure-remotes)
   so that you can merge back from master.
1. Find an [issue](https://github.com/dionyziz/endofcodes/issues) that you want to fix.
1. [Create a new branch](http://git-scm.com/book/en/Git-Branching-Basic-Branching-and-Merging)
   in your clone.
1. Write tests for your fix.
1. Implement your fix.
1. Make sure **all** unit tests pass.
1. Push your new branch with your changes to your fork.
1. Submit a pull request.

## Finding an issue
There are four ways to find something to do on the project:

1. You have a problem with the project or an idea of your own. In this case,
simply go ahead and implement your idea! It's not necessary that we have
thought of this idea - it can be your own. However, before you do that, please
look at the 
[spec](https://github.com/dionyziz/endofcodes/blob/master/SPECIFICATION.md).
This is important, because we may already have plans for this feature. In that
case, the plans may detail how we envision to build your idea and you may want
to follow these plans or at least discuss them with us before you go
ahead. Please also search for
[issues](https://github.com/dionyziz/endofcodes/issues?state=open)
that may mention your feature or bugfix. Somebody else may be working on it
already and you may want to talk to them. We don't want to do the same work
twice! If you find an issue and no one is working on it, leave a comment saying
you're starting to work on it (or assign it to yourself if you're a
collaborator). If you can't find an issue, please create one and mention that
you're working on it in a comment. If someone else is working on your issue
already, talk to them. They may need your help.

1. You want to fix an existing issue. Just go to the
[open issues page](https://github.com/dionyziz/endofcodes/issues?state=open),
pick an issue that you think is worthy, and start fixing it. Like before, don't
forget to mention in a comment that you're working on it (or assign it to
yourself) and make sure nobody else is doing the same work.

1. You want to implement a feature we're planning. Visit the
[spec](https://github.com/dionyziz/endofcodes/blob/master/SPECIFICATION.md)
and pick something you like. Then create an issue and mention that you'll
be working on it (or assign it to yourself). Make sure an issue doesn't already
exist for the feature you chose! If someone else is working on it, speak with
them.

1. Look at the code and see if you can refactor something. Look for
[broken windows](http://pragmatictips.com/4).

Please note that the spec is not a static document. If you have a big idea that
requires spec changes, go ahead and change the spec; then pull request these
changes. We will discuss new ideas in such pull requests.

## Reviewing pull requests
If you make a pull request with your change, we promise to review it within
3 days. Hopefully we will review it within 1 day - we try to be responsive.

Reviewing means you'll either get a comment with a request to change something,
or we'll merge your pull request. If we request a change and you make it, we'll
review you again.

Pull requests are reviewed by our peers, just like you. You can also review
pending pull requests by others too! Just go to 
[the list of open pull requests](https://github.com/dionyziz/endofcodes/pulls),
pick one you want to see merged, and see if the code looks good. If you see
some issue, leave a comment on the particular line of code, or on the pull
request itself. If everything looks good to you, leave a comment indicating
that it's ready to be merged. You can say "LGTM" for "Looks Good To Me". If
you're a collaborator, you can also merge pull requests directly in this case.

Try to make one pull request per issue. If you want to make two changes,
make *two* different branches **from master** and pull request. If multiple
changes depend on each other, then you should *still* make a different branch
for each change - but base the dependent branch on the branch it depends on
instead of master. After you make your changes, you should make two different
pull requests. First, make a pull request from the base branch (that the other
branch was based on). Finally, make a pull request for the dependent
branch.
Write a comment on your pull request of the dependent branch saying it depends
on a previous pull request by
[mentioning](https://github.com/blog/957-introducing-issue-mentions) the
previous pull request. We'll then review them in order.

If a pull request fixes an issue, mention the issue it fixes in your pull
request. When the pull request is merged, you can close the issue too.

Please don't merge your own pull requests! Peer review exists to ensure our
software is of good quality. Even the most experienced programmers make
mistakes. It's important that the code is seen by at least the person who
wrote it and someone else.

Also, never push directly to the upstream repo, only your fork. All code changes
must go through pull requests.

Note that we merge directly to master. As we're in a volatile first version, we
don't have a separate 'develop' branch yet. Anything that gets merged is
deployed to production within the next few days.

## Requirements
We try to ensure that the quality of the code we merge is decent. Here are
some things we look for:

1. The change fixes **a real problem**, introduces a new feature, or usefully
refactors existing code. We look for the GitHub issue it corresponds to to see
that it really does fix the problem, or at the spec to see that it really does
build what we envisioned.

1. The change **fixes one problem** and not multiple problems.
Or that it introduces one feature, not multiple. If the pull request can be
split up to multiple ones, we'll ask you to do so.

1. **The change is architecturally sound.** We want to keep our code organized
based on correct software design principles and maintain modularity,
extensibility, and orthogonality. We also value simplicity and elegance and
want to make sure we don't overabstract.

1. **The change is tested.** We test all our code changes in back-end code. We
aim for good coverage, so make sure all use cases are covered. This doesn't
include just all lines of code, but also all the categories of use cases you can
think of. We do not merge code that doesn't include unit tests. If you make a
bug fix, we require a
[regression test](https://en.wikipedia.org/wiki/Regression_testing#Background).
We recommend that you follow
[test-driven development](https://en.wikipedia.org/wiki/Test-driven_development)
principles and write tests before you implement.

1. **The tests pass.** This includes tests that were not written for the
specific change - all tests must pass. We do not merge a failing build. You can
run all tests using `php run testrun create all=yes` or simply `make` from the
command line, or use our web interface.  We use
[travis](https://travis-ci.org/dionyziz/endofcodes)
to automatically run tests in every pull request and on every merge. You should
also enable it in your fork so that tests run after each push.

1. **Screenshots are included.** We currently don't automatically test front-end
code. However, we ask you to provide a screenshot with any front-end change you
make. This includes HTML, CSS, and JS changes. If you're fixing a front-end bug,
please upload a screenshot of the buggy situation as well as the fixed
situation. If it's a new feature, include a screenshot of how it looks at every
state. These screenshots can be included in a pull request comment.

1. **Coding style guidelines are followed.** We're pretty strict about coding style
and will not merge until you fix formatting issues. This ensures consistency
across our codebase. Please look at
[some of our guidelines](https://dionyziz.com/Style). More importantly, try to
match the style of code in the same file and similar files. This is the defining
standard that you should follow.

1. **Master is merged.** If other changes have been merged since your pull request,
you must merge them into your branch before it gets merged.

## Commit access
If you contribute often, we'll give you commit access so that you can assign
issues to yourself, have issues assigned to you, or merge pull requests by
others. Please follow the guidelines above.

## License
Please be aware that we're an open source project and are working under the
[MIT license](https://en.wikipedia.org/wiki/MIT_License).
This means that by contributing to the project, you are agreeing to make your
modifications available to the world for ever, even if you change your mind
later.

## Blog
We maintain a [development blog](http://blog.endofcodes.com/). If you
contribute, we encourage you to blog about it so that our users are aware of
our whereabouts. You will be given blog write access along with commit access.
Just ask!
