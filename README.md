# Layout Field Type
The Layout Field Type is a field type that enables the user to add widget based elements
to whatever stream the layout field type is added to. For example: as a developer making
a website for a client I might use various different content blocks over and over again,
things like sections with full background images, two columns, three columns, or even
just a specific div wrapping an html block. This field type allows the developer to
develop these "widgets", as I call them, and use them throughout the website. What makes
this field type particularly useful is that the widgets are stored on the filesystem,
they can be committed to version control and changing a single twig file will change
every widget that has already been used on the entire site.

Watch the video <a href="#">here</a> to see a demonstration of the layout field type.

## Installation
The Layout Field Type comes with two addons. The first addon is the actual
layout field type, this will be in the directory layout_field-type. The second is a fully
functional widgets package with a few demonstration widgets built for bootstrap. This
extension is also a great place to store your bootstrap widget, or create a new extension,
module, theme, and store your widgets there. They can exist in whatever type of addon you
wish.

Place the layout_field-type directory inside /addons/\<YOUR SITEREF\>/fritzandandre.
There is no installer that needs to be run. You can now start using the layout field type
in your site.

Additionally, if you would like to use the bootstrap widgets that I've provided with the
field type you'll need to place those widgets in the same location /addons/\<YOUR
SITEREF\>/fritzandandre. The bootstrap widgets will need to be installed since they store
their information in the database. So visit \<YOUR URL\>/admin/addons/extensions to
install the bootstrap widgets.

## Configuration
Since the layout field type is configured by it's widgets there is no particular
configuration for the layout field itself. See "Generating Widgets" for more information.

## Usage
I will be using the pages module as an example usage. There are only a few steps
necessary to begin using the layout field type with page and this process can be adapted
to any other stream quite easily.

After installing the field type as mentioned above (and for this example installing the
bootstrap widgets), we can create a new field in pages. Visit
\<YOUR SITE\>/admin/pages/fields and create a "New Field" select "Layout" from the popup.
Give your field a name. For mine I've used "Page Layout" then save the field.

Next, we will add the new layout field to a page type. Visit
\<YOUR SITE\>/admin/pages/types and edit the "Assignments" for the page type you wish to
add the layout field type to. Assign a new field to the page type and select whatever
name you used in the previous step to assign the layout field type to your page. Last,
return to \<YOUR SITE\>/admin/pages/types and "Edit" the page layout you are adding the
layout field type to. We need to update the page type "Page Layout" to contain our new
field type. Add ```{{ page.page_layout|raw }}``` wherever in your page layout that you
would like the widgets from the layout field type to be displayed.

Now we are done! You can begin adding widgets to your pages! And when you have moved on
past the abilities of the default widgets and you are ready to begin creating your own
widgets with your own custom functionality then move on to "Generating Widgets".

Watch the video <a href="#">here</a> to see a demonstration of the layout field type.

## Generating Widgets
A convenient artisan tools has been made to allow the generation of widgets automatically.
Running ```php artisan make:widget namespace widget_slug``` will generate a new widget in
whatever namespace was used in the ```make:widget``` command, such as ```widget_slug```.
For example if I wanted to add a new widget to fritzandandre.module.test called
content_widget, I would run 
```php artisan make:widget fritzandandandre.module.test content``` and I would get a new
database migration for my widget as well as all the code to enable the widget and
render it.

After running the command there are a few files that need to be updated in order to
customize this widget for your use. First navigate to the root directory of the containing
module (in this case fritzandandre.module.test) and you'll see a, possibly, new directory
called addons. Widgets are created as extensions that exist within an existing addon, so
navigate inside addons/fritzandandre (this will actually be whatever vendor name you used
in the make:widget command) and you'll see you new widget-extension in that directory.
 I've tried to provide some reasonable defaults to get started with, but you'll absolutely
 have to change them to customize the widget for your use.
 
 First, the migration for the widget is added to the parent addon, so go into the migrations
 folder of the addon you added the widget to and edit that migration to contain the fields
 and assignments that you will need for your widget. I prefer to use a single migration 
 that contains the stream definition, fields, assignments, and a custom namespace for the
 widget. These are all completely optional and you can change the migration as must as you
 need.
 
 Second, open src/ContentWidget/ContentWidgetExtension.php there is a single function in that
 file that you'll want to update, being the render function. By default the widget has only
 one field called "content". You'll want to add your fields to this render function and
 any other customizations necessary to render your widget. I usually only need to update the
 fields passed into the view in this function, but you can perform any other pre-processing
 steps necessary here.
 
 Third, open resources/views/render.twig and you can alter how this widget is displayed
 when the layout field type is rendered on the front-end of your application.
 
 Fourth, open resources/lang/en/addon.php and give your widget a good description. This
 description and the widget name are the only things visible when a user is selecting from
 the list of widgets, so be descriptive and tell the use what your widget is all about.
 
 Watch the video <a href="#">here</a> to see a demonstration of generating widgets.

## Changelog

 v1.0.0 Initial release

 v1.0.1 Layout FT will not throw an error if a widget is unistalled or deleted from the system. Layout FT will automatically clean rows from the DB if the related widget class is removed from the system.
