<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services\WPQuery\Traits;

trait BuildsTaxonomyQueryArguments
{
	/**
	 * Builds a taxonomy query arguments array
	 *
	 * @param string           $taxonomy
	 * @param string           $field
	 * @param string           $operator
	 * @param int|string|array $terms
	 * @param bool             $children
	 *
	 * @return array
	 */
	protected function buildTaxonomyQueryArgument(
		$taxonomy,
		$field = 'term_id',
		$operator = 'IN',
		$terms = null,
		$children = true
	) {
		if (is_array($taxonomy)) {
			return $taxonomy;
		}

		// If the taxonomy is a callback, we assume that that callback is going to build
		// the array. To make things easier for the developer, we insert this method as
		// a Closure to that callback. That way you can simply build up an nested array
		// just like you would build up other meta query arguments.
		if ($taxonomy instanceof \Closure) {
			return $taxonomy($this->getTaxonomyBuilderAsClosure());
		}

		$countArgs = func_num_args();

		// If there are only two values passed to the method we will assume that field
		// represents the terms instead.
		if (2 == $countArgs) {
			return $this->whereTermsMatchTaxonomyTermID($taxonomy, $field);
		}

		// If 3 values are passed to the method we will assume that the operator
		// is 'IN' and that it represents the terms instead.
		if (3 == $countArgs) {
			return $this->whereTaxonomyFieldMatchesTerms($taxonomy, $field, $operator);
		}

		return [
			'taxonomy' => $taxonomy,
			'field' => $field,
			'terms' => $terms,
			'operator' => $operator,
			'include_children' => $children,
		];
	}

	/**
	 * Query where terms match a particular term its ID.
	 *
	 * @param $taxonomy
	 * @param $term
	 *
	 * @return array
	 */
	protected function whereTermsMatchTaxonomyTermID($taxonomy, $term)
	{
		return [
			'taxonomy' => $taxonomy,
			'terms' => $term,
		];
	}

	/**
	 * Query a taxonomy where a field matches certain terms.
	 *
	 * @param $taxonomy
	 * @param $field
	 * @param $operator
	 *
	 * @return array
	 */
	protected function whereTaxonomyFieldMatchesTerms($taxonomy, $field, $operator)
	{
		return [
			'taxonomy' => $taxonomy,
			'field' => $field,
			'terms' => $operator,
		];
	}

	/**
	 * Creates a closure of the taxonomy builder method.
	 *
	 * @return \Closure
	 */
	protected function getTaxonomyBuilderAsClosure()
	{
		return (new \ReflectionMethod($this, 'buildTaxonomyQueryArgument'))->getClosure($this);
	}
}
