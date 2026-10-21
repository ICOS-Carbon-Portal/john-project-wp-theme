# JOHN Project WordPress theme

A Twenty Twenty-Four child theme for <https://john-project.eu>.

The footer uses the official "Funded by the European Union" emblem in the
version for dark backgrounds (colour flag with a white border, white text),
from the EU's English emblem pack (`EN_FundedbytheEU_RGB_NEG.png`), resized
only.

The favicons in `assets/images/favicons/` are the three waves from the logo.
The theme always uses them, also for `/favicon.ico`, and ignores any Site
Icon set in WordPress, so WordPress's default icon never shows.

## Requirements

WordPress 7.0 or later, for the core Breadcrumbs block. No plugins are needed.

## Site setup

- Settings, Reading: set a page as the homepage (e.g. "Home") and a page as the
  posts page (e.g. "News").
- Build the menu in Appearance, Editor, Navigation. The header uses whichever
  menu exists; with none, it lists published pages.
- Events have their own admin menu. Their archive is at
  `/news-events/events/` (single events below it), so don't create a page
  called "Events" under News & Events. The `news-events` part is fixed in the
  theme and doesn't follow the page's slug.
- Some links find pages by slug, so keep these slugs: `about` and `objectives`
  (home page buttons), `newsletter` (subscribe buttons and footer),
  `accessibility-statement` (footer). Until a page exists, its link points to
  `#`. The footer's privacy link uses Settings, Privacy.
- The footer's "Explore" list shows the pages with the slugs `about`,
  `objectives`, `results`, `news-events` and `newsletter`, in that order, when
  they exist and are published.
- "All news & events" links go to the parent page of the posts page (e.g.
  "News & Events").
- A page's "See also" card is chosen in the page editor's sidebar and shows
  the chosen page's excerpt, so give that page an excerpt.

## License

GNU General Public License v2 or later.
