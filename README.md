# Moxie Test

Download the plugin from https://github.com/vampaynani/moxie_test/releases/tag/1.0.0

When the user enables the plugin, it does many things:
- Creates an endpoint on: **domain.com/movies.json**
- Creates a new post type *movie* in the admin view
- Creates a page called *Movies* and set it as frontpage.

Also enables a hook, this  does updating and caching at the moment you update or create a *movie* post.

##Todos
- Create an admin section to define if the user will like the frontpage behavior.
- Add to the endpoint a **/detail.json** for each movie.
- Add *infinite scrolling* to the *WP Frontpage*.