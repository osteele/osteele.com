*****************************************************************************
                      T A X O N O M Y    T H E M E
*****************************************************************************
Name: taxonomy_theme module
Author: Thilo Wawrzik <drupal at profix898 dot de>
Drupal: 5.0
*****************************************************************************
DESCRIPTION:

The taxonomy_theme module allows you to change the theme of a given node
based on the taxonomy term, vocabulary or nodetype of that node. You can
also theme your forums and map themes to Drupal paths or path aliases
directly.

*****************************************************************************
INSTALLATION:

1. Place whole reptag folder into your Drupal modules/ directory.

2. Enable taxonomy_theme module by navigating to
     Administer > Site Building > Modules (admin/build/modules)
     
3. Bring up taxonomy_theme configuration screen by navigating to
     Administer > Site Configuration > Taxonomy Theme
    (admin/settings/taxonomy_theme)
     
4. Configure all settings after your fancy (see below)

!! Enable all your themes once to make them available for taxonomy_theme
!! and don't forget to configure block settings per theme.

!! Taxonomy Access users need to grand 'LIST' permission for every term
!! they want to change the theme on (otherwise default theme is applied). 

*****************************************************************************
UPDATE:

1. Replace all files with the latest version 
     
2. Run update.php to migrate your settings (in this case only data from
   'extended' assignment). It ensures that all tables are created and
   all update operations are completed properly.
   
3. Enable all your themes once to make them available for taxonomy_theme
   and don't forget to configure block settings per theme (core feature
   since Drupal 4.7).

*****************************************************************************
SETTINGS / OPTIONS:

Most options should be pretty much self-explanatory. But some may not! 

- "Allow all themes"
Offers all installed themes for selection, not enabled ones only.
By default you can only assign active themes to pages (themes can be
activated at administer > themes). But active themes are also presented
to your users, so they can select their favorite theme. You may want to
have different themes applied to your site without giving your users the
choice. This is what 'Allow all themes' option is for. You can also
prevent your users from selecting their personal theme by removing the
'select different theme' (system module) permission.

- "Enable 'Extended' (path-based) assignment of themes"
You can assign themes to every Drupal path and path alias. To assign a
theme for a particular path enter the path into according textarea in
'Extended (Pathes)' section (visible only with this option enabled).
Extended options have higher priority. Themes assigned to paths
override themes from taxonomy selection methods (e.g. vocab-based).

- "Enable 'Themes for Views'"
This option is available only with views.module installed. You also need
to have 'Extended' option enabled. It uses hook_form_alter to add a
theme selection box to edit view pages.

- "Show theme option in create/edit node forms"
This option adds a 'Theme' section on create/edit node pages
(i.e. create/edit content). You need to have 'Extended' option enabled
to use this option. It enables you to assign themes directly from
create/edit node forms. 

- "Show theme option on term/vocab/content-types pages"
This option adds a theme selection box to term/vocab pages in
'Categories' and to content-type settings page. It depends on your
taxonomy selection method what forms are altered, e.g. with
'vocab-based' method selected only 'edit vocabulary' pages
(admin/content/taxonomy/edit/vocabulary/x) are extended.

Selecting 'default' from the theme selection boxes, disables 'Extended'
assignment for this node/path/... and uses the theme selected from
taxonomy selection method (e.g. vocab-based) instead. What means you
dont need to select a theme for each node, but you can optionally do for
more precise adjustment. Path-based themes override Taxonomy-based ones!

- "'Admin Area' - Theme"
This option is now a core feature. You can find it at
 Administer > Site Configuration > Taxonomy Theme (admin/settings/admin)

- "Taxonomy: Template selection"
You can choose one of four different methods here.

+ Option A: Term-based
With this method you will have a 'Theme-Selector' vocabulary and you can
assign one theme to each of its terms. When you create/edit a node you
map a term to it and therefore map a theme to it also.

 Decide which vocabulary you will use to control the selection of
  templates, and make sure that vocabulary exists, e.g. create a
  'Sections' vocabulary.
 Create terms that will select templates, e.g. if 'Section A' of your
  site will have one template and 'Section B' will have another template,
  create the terms 'Section A' and 'Section B' in 'Sections' vocabulary.
 Choose the vocabulary you will use to control the selection of templates
  and map the terms to templates.

+ Option B: Vocab[ulary]-based
If you have 'Section A' and 'Section B' as two different vocabularies and
want to assign 'Theme A' and 'Theme B' to a whole Section including all
its (sub)terms, use this 'Vocab-based' method. 

+ Option C: All Taxonomy
With the 'all taxonomy' method you can select themes for all terms in all
categories/vocabularies, what gives you even more control.
 All Taxonomy = Term-based + Vocab-based

+ Option D: NodeType-based
If you need to apply different themes for certain node types, you can use
NodeType-based selection method.

!!!
You can set themes globally with Option B,C and D, so that your users
dont need to select a term on each page for theme selection. In case your
users are allowed to create content (pages, stories, ...) it might be
more safe to use than Option A.
!!!

- "Apply themes to forums"
With this option enabled you can theme your forums different from the
rest of your site and/or theme each subforum independently. Since you
can only have one forum vocabulary this is necessary to use forums in
different themed context on your site.

- "Theme forum-dependent nodes/pages"
Generally only the forums (list of topics or comments) are themed, but
add/edit comment forms remain in default site/section layout. Enable
this option (very recommended) to apply forum themes to all
forum-related pages/nodes also.

*****************************************************************************
Enjoy theming your Site. Thilo.
