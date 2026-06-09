# Taxonomy Control Extension Design

**Date:** 2026-06-09

## Goal

Extend the taxonomy filter control to mirror the ConnectionControl pattern: add a radio toggle between "current post taxonomies" (auto-fill) and "specific terms" (existing behaviour), and surface the existing `taxonomyRelation` AND/OR attribute as an editor control.

---

## Attributes

### New in `block.json`

```json
"taxonomyOption": {
  "type": "string",
  "default": "specific-terms"
},
"taxonomyRelation": {
  "type": "string",
  "default": "AND"
}
```

`taxonomyRelation` already exists in `BlockAttributes.php` but was missing from `block.json` — adding it makes it actually persist from editor to PHP.

`taxonomyOption` default `'specific-terms'` ensures backwards compatibility — existing blocks continue working unchanged.

### `BlockAttributes.php`

Add:
```php
public string $taxonomyOption = 'specific-terms',
```

PHP does not act on `taxonomyOption`; terms are stored in `taxonomyTerms` regardless of mode. PHP reads `taxonomyTerms` and `taxonomyRelation` as before — no query builder changes needed.

---

## UI Structure

When `enableTaxonomies = true`:

```
TaxonomyToggleControl                         (existing)

TaxonomyRadioControl                          (new)
  ● Toon berichten van dezelfde taxonomie
    als het huidige bericht                   → 'current-post-taxonomies'
  ○ Toon berichten van specifieke
    taxonomie terms                           → 'specific-terms'

── when 'current-post-taxonomies' ──
  Read-only term chips per taxonomy           (auto-filled, not editable)
  No selects shown.

── when 'specific-terms' ──
  TaxonomySelectControl × N                  (existing, unchanged)

── when taxonomies.length >= 2 (both modes) ──
  TaxonomyRelationControl                    (new)
    ● AND  posts must match all taxonomies
    ○ OR   posts match any taxonomy
```

---

## New Components

### `taxonomy-radio-control.js`

RadioControl with two options. On change: sets `taxonomyOption` and resets `taxonomyTerms: {}`. Mirrors `ConnectionRadioControl` exactly.

```js
const TAXONOMY_OPTIONS = [
  {
    label: __('Toon berichten van dezelfde taxonomie als het huidige bericht', 'yard-query-block'),
    value: 'current-post-taxonomies',
  },
  {
    label: __('Toon berichten van specifieke taxonomie terms', 'yard-query-block'),
    value: 'specific-terms',
  },
];

const handleChange = (value) => {
  setAttributes({ taxonomyOption: value, taxonomyTerms: {} });
};
```

### `taxonomy-relation-control.js`

RadioControl AND/OR. Visible when `taxonomies.length >= 2` in both modes.

```js
const RELATION_OPTIONS = [
  { label: __('EN (berichten moeten aan alle taxonomieën voldoen)', 'yard-query-block'), value: 'AND' },
  { label: __('OF (berichten mogen aan één van de taxonomieën voldoen)', 'yard-query-block'), value: 'OR' },
];
```

---

## Auto-fill Logic (in `taxonomy-control.js`)

Uses WP REST API `?post={id}` filter — returns terms assigned to a specific post. No custom endpoint needed.

```js
useEffect(() => {
  if (!enableTaxonomies || taxonomyOption !== 'current-post-taxonomies') return;
  if (!currentPostId || taxonomies.length === 0) return;

  const fillTerms = async () => {
    const newTerms = {};
    for (const taxonomy of taxonomies) {
      const terms = await apiFetch({
        path: `/wp/v2/${taxonomy.rest_base}?post=${currentPostId}&per_page=100`
      });
      if (terms?.length) {
        newTerms[taxonomy.slug] = terms.map(t => ({ label: t.name, value: t.slug }));
      }
    }
    setAttributes({ taxonomyTerms: newTerms });
  };

  fillTerms();
}, [taxonomyOption, enableTaxonomies, currentPostId, taxonomies]);
```

`currentPostId` comes from `useSelect` on `core/editor`. Edge case: current post has no terms in a taxonomy → key omitted from `taxonomyTerms`. PHP `hasTaxonomyFilter()` already handles sparse `taxonomyTerms` objects.

---

## PHP Changes

| File | Change |
|---|---|
| `block.json` | Add `taxonomyOption` and `taxonomyRelation` attributes |
| `BlockAttributes.php` | Add `public string $taxonomyOption = 'specific-terms'` |
| `PostQuery.php` | No changes — query path unchanged |

---

## Out of Scope (Later)

- **Level 2 AND/OR:** Per-taxonomy operator (IN / AND / NOT IN) for within-taxonomy term matching. Noted for future addition as a toggle per taxonomy in specific-terms mode.

---

## File Change Summary

| File | Type |
|---|---|
| `block.json` | Modify |
| `src/Block/BlockAttributes.php` | Modify |
| `assets/components/filters-controls/taxonomy-control.js` | Modify |
| `assets/components/filters-controls/taxonomy-radio-control.js` | New |
| `assets/components/filters-controls/taxonomy-relation-control.js` | New |
