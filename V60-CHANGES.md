# MOKAWA GROUP WEBSITE — V60 CHANGES

## Summary
Kiikalloh Grand Resort's management contract with Mokawa Group has ended.
Per direction, Kiikalloh Grand Resort has been fully removed from active
navigation, footers, and the homepage — mirroring exactly how Eldon Villas
(also formerly managed) is already handled on this site: no banners, no
"former partner" flags, no dedicated section. Kiikalloh is now mentioned
in exactly one place — a plain-text line in the Mokawa Story timeline —
with no link, matching the existing Eldon Villas mention.

## 1. Removed Kiikalloh Grand Resort from active site areas

**index.html**
- Removed from meta description, meta keywords, and og:description.
- Removed the JSON-LD "currently managed hotels" ItemList entry.
- Removed from the navigation dropdown menu.
- Removed the hero image-carousel slide.
- Removed the property tab/booking panel from "Our Properties."
- Removed the (previously added, now deleted) "Former Partnerships"
  section entirely — Kiikalloh no longer appears on the homepage at all.
- Removed the footer link.
- "Properties Managed" stat: 3 (Silver Rock, Paradise Farm, Eldon Villas).

**services.html, hotels/silver-rock.html, hotels/paradise-farm.html,
hotels/eldon-villas.html, hotels/silver-rock-backup.html**
- Removed the Kiikalloh nav-dropdown entry and footer link wherever it
  appeared, matching the fact that Eldon Villas is not cross-linked from
  these dropdowns either.

**hotels/kiikalloh-resort.html**
- Restored to its normal, unflagged state (booking buttons, hero, and CTA
  content back to original) — same treatment as the Eldon Villas page,
  which carries no "formerly managed" notice either. The page still exists
  on disk but is no longer linked from anywhere on the site.

**sitemap.xml**
- Removed the Kiikalloh Grand Resort entry.

**okawa-story.html**
- The only remaining mention of Kiikalloh on the entire site: a single
  plain-text sentence in the company timeline noting the past partnership
  (no link), styled identically to the existing Eldon Villas mention in
  the same timeline.
- Removed the nav-dropdown entry for Kiikalloh on this page too.

## 2. SEO — Megzier Group Ltd developer attribution
Kept across all HTML pages (unrelated to the Kiikalloh change):
- `<meta name="developer" content="This website is managed and developed by
  Megzier Group Ltd">`
- `<meta name="designer" content="Megzier Group Ltd">`
- Footer copyright lines: "Website managed and developed by Megzier Group
  Ltd."

## Files touched
- index.html
- okawa-story.html
- services.html
- sitemap.xml
- hotels/kiikalloh-resort.html
- hotels/silver-rock.html
- hotels/paradise-farm.html
- hotels/eldon-villas.html
- hotels/silver-rock-backup.html
