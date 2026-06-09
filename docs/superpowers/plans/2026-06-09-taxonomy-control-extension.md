# Taxonomy Control Extension Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend the taxonomy filter control with a radio toggle between "current post terms" (auto-fill) and "specific terms" (existing), and surface the existing `taxonomyRelation` AND/OR attribute in the editor UI.

**Architecture:** Two new JS radio controls (`TaxonomyRadioControl`, `TaxonomyRelationControl`) are wired into the existing `TaxonomyControl` orchestrator. In "current post" mode, a `useEffect` auto-fetches the current post's terms via the WP REST API and writes them into `taxonomyTerms` — the same attribute PHP already reads. No PHP query changes needed; only `block.json` and `BlockAttributes.php` gain the two new attributes.

**Tech Stack:** React (via `@wordpress/element`), `@wordpress/components` RadioControl, `@wordpress/data` useSelect, `@wordpress/api-fetch`, PHP Pest tests.

---

## File Map

| File | Action |
|---|---|
| `assets/block.json` | Modify — add `taxonomyOption`, `taxonomyRelation` attributes |
| `src/Block/BlockAttributes.php` | Modify — add `$taxonomyOption` property |
| `tests/Block/BlockAttributesTest.php` | Modify — add tests for `$taxonomyOption` default |
| `assets/components/filters-controls/taxonomy-radio-control.js` | Create |
| `assets/components/filters-controls/taxonomy-relation-control.js` | Create |
| `assets/components/filters-controls/taxonomy-control.js` | Modify — wire new controls + auto-fill logic |

---

## Task 1: Add `taxonomyOption` and `taxonomyRelation` to `block.json`

**Files:**
- Modify: `assets/block.json`

- [ ] **Step 1: Add attributes**

Open `assets/block.json`. After the `"taxonomyTerms"` attribute block, add:

```json
"taxonomyOption": {
  "type": "string",
  "default": "specific-terms"
},
"taxonomyRelation": {
  "type": "string",
  "default": "AND"
},
```

The file already has `"enableTaxonomies"` and `"taxonomyTerms"` — insert after `taxonomyTerms`. Do NOT touch `public/block.json` (build output).

- [ ] **Step 2: Verify JSON is valid**

```bash
cd /Users/yvettenikolov/LocalSites/nbbu/app/public/vendor/yard/wp-query-block
node -e "JSON.parse(require('fs').readFileSync('assets/block.json','utf8')); console.log('valid')"
```

Expected: `valid`

- [ ] **Step 3: Commit**

```bash
git add assets/block.json
git commit -m "feat(block): add taxonomyOption and taxonomyRelation block attributes"
```

---

## Task 2: Add `$taxonomyOption` to `BlockAttributes.php` + tests

**Files:**
- Modify: `src/Block/BlockAttributes.php`
- Modify: `tests/Block/BlockAttributesTest.php`

- [ ] **Step 1: Write failing test**

In `tests/Block/BlockAttributesTest.php`, add at the end of the file:

```php
it('returns specific-terms as default taxonomyOption', function () {
    $attributes = BlockAttributes::from([]);
    expect($attributes->taxonomyOption)->toBe('specific-terms');
});

it('returns the given taxonomyOption', function () {
    $attributes = BlockAttributes::from(['taxonomyOption' => 'current-post-taxonomies']);
    expect($attributes->taxonomyOption)->toBe('current-post-taxonomies');
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run from the project root (`wp-query-block/`):

```bash
composer test -- --filter="taxonomyOption"
```

Expected: FAIL — `Undefined property: Yard\QueryBlock\Block\BlockAttributes::$taxonomyOption`

- [ ] **Step 3: Add property to `BlockAttributes.php`**

In `src/Block/BlockAttributes.php`, find the constructor. Add `$taxonomyOption` directly after `$taxonomyRelation`:

```php
public string $taxonomyOption = 'specific-terms',
```

The constructor already has `public string $taxonomyRelation = 'AND',` — add the new line right after it. PHP does not need to act on this value; the property is declared for attribute persistence only.

- [ ] **Step 4: Run tests to verify they pass**

```bash
composer test -- --filter="taxonomyOption"
```

Expected: PASS (2 tests)

- [ ] **Step 5: Run full test suite**

```bash
composer test
```

Expected: all existing tests still pass.

- [ ] **Step 6: Commit**

```bash
git add src/Block/BlockAttributes.php tests/Block/BlockAttributesTest.php
git commit -m "feat(php): add taxonomyOption property to BlockAttributes"
```

---

## Task 3: Create `TaxonomyRadioControl`

**Files:**
- Create: `assets/components/filters-controls/taxonomy-radio-control.js`

This mirrors `connection-radio-control.js` exactly. On mode switch it resets `taxonomyTerms` to `{}` so stale terms from the previous mode don't leak through.

- [ ] **Step 1: Create the file**

```js
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RadioControl } from '@wordpress/components';

const TAXONOMY_OPTIONS = [
	{
		label: __(
			'Toon berichten van dezelfde taxonomie als het huidige bericht',
			'yard-query-block'
		),
		value: 'current-post-taxonomies',
	},
	{
		label: __(
			'Toon berichten van specifieke taxonomie terms',
			'yard-query-block'
		),
		value: 'specific-terms',
	},
];

const TaxonomyRadioControl = ( { taxonomyOption, setAttributes } ) => {
	const handleChange = ( value ) => {
		setAttributes( {
			taxonomyOption: value,
			taxonomyTerms: {},
		} );
	};

	return (
		<RadioControl
			label={ __( 'Taxonomie opties', 'yard-query-block' ) }
			hideLabelFromVision={ true }
			selected={ taxonomyOption }
			options={ TAXONOMY_OPTIONS }
			onChange={ handleChange }
		/>
	);
};

export default TaxonomyRadioControl;
```

- [ ] **Step 2: Verify build compiles**

```bash
npm run build 2>&1 | tail -5
```

Expected: exits without errors (the file is not imported anywhere yet — that's fine, it just needs to compile).

- [ ] **Step 3: Commit**

```bash
git add assets/components/filters-controls/taxonomy-radio-control.js
git commit -m "feat(js): add TaxonomyRadioControl component"
```

---

## Task 4: Create `TaxonomyRelationControl`

**Files:**
- Create: `assets/components/filters-controls/taxonomy-relation-control.js`

Shown when 2+ taxonomies are available (both "current post" and "specific terms" modes). Controls the `taxonomyRelation` attribute — which was already wired in PHP but had no editor UI.

- [ ] **Step 1: Create the file**

```js
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RadioControl } from '@wordpress/components';

const RELATION_OPTIONS = [
	{
		label: __(
			'EN (berichten moeten aan alle taxonomieën voldoen)',
			'yard-query-block'
		),
		value: 'AND',
	},
	{
		label: __(
			'OF (berichten mogen aan één van de taxonomieën voldoen)',
			'yard-query-block'
		),
		value: 'OR',
	},
];

const TaxonomyRelationControl = ( { taxonomyRelation, setAttributes } ) => {
	const handleChange = ( value ) => {
		setAttributes( { taxonomyRelation: value } );
	};

	return (
		<RadioControl
			label={ __( 'Relatie tussen taxonomieën', 'yard-query-block' ) }
			selected={ taxonomyRelation }
			options={ RELATION_OPTIONS }
			onChange={ handleChange }
		/>
	);
};

export default TaxonomyRelationControl;
```

- [ ] **Step 2: Verify build compiles**

```bash
npm run build 2>&1 | tail -5
```

Expected: exits without errors.

- [ ] **Step 3: Commit**

```bash
git add assets/components/filters-controls/taxonomy-relation-control.js
git commit -m "feat(js): add TaxonomyRelationControl component"
```

---

## Task 5: Wire everything into `TaxonomyControl`

**Files:**
- Modify: `assets/components/filters-controls/taxonomy-control.js`

This is the main orchestration task. Adds:
- `useSelect` for `currentPostId`
- `useEffect` for auto-fill in "current post" mode
- `TaxonomyRadioControl` rendered when toggle is on
- `TaxonomyRelationControl` rendered when toggle is on AND `taxonomies.length >= 2`
- `TaxonomySelectControl` rendered only in "specific-terms" mode

- [ ] **Step 1: Replace `taxonomy-control.js` with the updated version**

```js
/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import TaxonomyToggleControl from './taxonomy-toggle-control';
import TaxonomyRadioControl from './taxonomy-radio-control';
import TaxonomyRelationControl from './taxonomy-relation-control';
import TaxonomySelectControl from './taxonomy-select-control';
import { fetchTaxonomiesByPostType } from '../../utils/api';
import { filterTaxonomies } from '../../utils/taxonomies';

const TaxonomyControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		enableTaxonomies,
		enableManualSelection,
		taxonomyOption,
		taxonomyRelation,
	} = attributes;
	const [ taxonomies, setTaxonomies ] = useState( [] );

	const { currentPostId } = useSelect(
		( select ) => ( {
			currentPostId: select( 'core/editor' ).getCurrentPostId(),
		} ),
		[]
	);

	/**
	 * Fetch taxonomies of selected post types
	 */
	useEffect( () => {
		const getTaxonomies = async () => {
			let allTaxonomies = {};

			for ( const key in postTypes ) {
				const typeTaxonomies = await fetchTaxonomiesByPostType(
					postTypes[ key ].value
				);
				allTaxonomies = { ...allTaxonomies, ...typeTaxonomies };
			}

			const filteredTaxonomies = filterTaxonomies( allTaxonomies );

			setTaxonomies( filteredTaxonomies );
		};

		getTaxonomies();
	}, [ postTypes ] );

	/**
	 * Auto-fill taxonomyTerms from the current post when in 'current-post-taxonomies' mode.
	 * Fetches terms per taxonomy using the WP REST API ?post={id} filter.
	 */
	useEffect( () => {
		if (
			! enableTaxonomies ||
			taxonomyOption !== 'current-post-taxonomies'
		) {
			return;
		}

		if ( ! currentPostId || taxonomies.length === 0 ) {
			return;
		}

		const fillTerms = async () => {
			const newTerms = {};

			for ( const taxonomy of taxonomies ) {
				const terms = await apiFetch( {
					path: `/wp/v2/${ taxonomy.rest_base }?post=${ currentPostId }&per_page=100`,
				} );

				if ( terms?.length ) {
					newTerms[ taxonomy.slug ] = terms.map( ( t ) => ( {
						label: t.name,
						value: t.slug,
					} ) );
				}
			}

			setAttributes( { taxonomyTerms: newTerms } );
		};

		fillTerms();
	}, [ taxonomyOption, enableTaxonomies, currentPostId, taxonomies, setAttributes ] );

	return (
		! enableManualSelection &&
		taxonomies.length !== 0 && (
			<>
				<TaxonomyToggleControl { ...props } />

				{ enableTaxonomies && (
					<TaxonomyRadioControl
						taxonomyOption={ taxonomyOption }
						setAttributes={ setAttributes }
					/>
				) }

				{ enableTaxonomies &&
					taxonomyOption === 'specific-terms' &&
					taxonomies.map( ( taxonomy ) => {
						return (
							<div key={ taxonomy.slug }>
								<TaxonomySelectControl
									taxonomy={ taxonomy }
									{ ...props }
								/>
							</div>
						);
					} ) }

				{ enableTaxonomies && taxonomies.length >= 2 && (
					<TaxonomyRelationControl
						taxonomyRelation={ taxonomyRelation }
						setAttributes={ setAttributes }
					/>
				) }
			</>
		)
	);
};

export default TaxonomyControl;
```

- [ ] **Step 2: Build**

```bash
npm run build 2>&1 | tail -10
```

Expected: exits without errors, `public/` assets updated.

- [ ] **Step 3: Verify in editor**

Open the WordPress block editor on any post that has the yard/query block. Enable "Filter op taxonomie". Verify:

1. Radio appears with two options — "dezelfde taxonomie als het huidige bericht" selected by default in new blocks; "specifieke taxonomie terms" in existing blocks.
2. Selecting "specifieke taxonomie terms" shows the AsyncSelect controls per taxonomy (existing behaviour unchanged).
3. Selecting "dezelfde taxonomie als het huidige bericht" hides the AsyncSelect controls and auto-fills `taxonomyTerms` in block attributes (check via block editor "Edit as HTML" or browser DevTools → block attributes).
4. When 2+ taxonomies are configured for the post type, the AND/OF radio appears below the selects (or below the info text in current-post mode).
5. Switching modes resets `taxonomyTerms` (stale terms from the previous mode don't persist).

- [ ] **Step 4: Commit**

```bash
git add assets/components/filters-controls/taxonomy-control.js
git commit -m "feat(js): wire TaxonomyRadioControl and TaxonomyRelationControl into TaxonomyControl"
```

---

## Task 6: Run full test suite + final build

- [ ] **Step 1: PHP tests**

```bash
composer test
```

Expected: all pass.

- [ ] **Step 2: Production build**

```bash
npm run build
```

Expected: exits without errors.

- [ ] **Step 3: Lint JS**

```bash
npm run lint:js
```

Expected: no errors (warnings about `setAttributes` in `useEffect` deps are acceptable if eslint-disable is needed — but try without first).

- [ ] **Step 4: Final commit if lint required any fixes**

```bash
git add -p
git commit -m "fix(js): lint fixes for taxonomy control extension"
```

Only commit if step 3 required changes.
