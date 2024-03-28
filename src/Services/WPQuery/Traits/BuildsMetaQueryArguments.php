<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services\WPQuery\Traits;

trait BuildsMetaQueryArguments
{
	/**
	 * Build an array to query posts/pages associated with a certain custom field.
	 *
	 * @param string|array    $key
	 * @param null|string     $operator
	 * @param null|string|int $value
	 * @param string          $type
	 *
	 * @return array
	 */
	protected function buildMetaQueryArgument($key, $operator = null, $value = null, $type = 'CHAR')
	{
		// If first value passed to the method is an array we assume that the array
		// represents meta query arguments. This is especially useful if you want to
		// build complex queries with nested arrays.
		if (is_array($key)) {
			return $key;
		}

		// If the key is a callback, we assume that that callback is going to build
		// the array. To make things easier for the developer, we insert this method as
		// a Closure to that callback. That way you can simply build up an nested array
		// just like you would build up other meta query arguments.
		if ($key instanceof \Closure) {
			return $key($this->getMetaBuilderAsClosure());
		}

		// Count the number of arguments passed to the method.
		$countArgs = func_num_args();

		// If the amount of values passed to method is one we assume we are only
		// checking if the meta key is present / not empty.
		if (1 == $countArgs) {
			return $this->whereMetaKeyIsNotEmpty($key);
		}

		// If there are only two values supplied to the method we will assume
		// that the operator is an equal sign, and that the second value supplied
		// to the method is what the key should match.
		if (2 == $countArgs) {
			return $this->whereMetaKeyMatches($key, $operator);
		}

		// If the value is numeric we will set the type to numeric automatically.
		// This will make sure that the value is processed by WordPress as an
		// integer instead of a string. Sorting will be alphabetical by defaultwhich is
		// fine for strings (i.e. words), but can be unexpected for numbers
		// (e.g. 1, 3, 34, 4, 56, 6, etc, rather than 1, 3, 4, 6, 34, 56 as you might naturally expect).
		if ($this->checkIfValueIsNumeric($value)) {
			$type = 'NUMERIC';
		}

		return [
			'key' => $key,
			'value' => $value,
			'compare' => $operator,
			'type' => $type,
		];
	}

	/**
	 * Queries posts/pages where a the meta key is present.
	 *
	 * @param $key
	 *
	 * @return array
	 */
	protected function whereMetaKeyIsNotEmpty($key)
	{
		return [
			'key' => $key,
		];
	}

	/**
	 * Query posts/pages where a meta key matches a certain value.
	 *
	 * @param string     $key
	 * @param int|string $value
	 *
	 * @return array
	 */
	protected function whereMetaKeyMatches($key, $value)
	{
		$args = [
			'key' => $key,
			'value' => $value,
			'compare' => '=',
		];

		if ($this->checkIfValueIsNumeric($value)) {
			$args['type'] = 'NUMERIC';
		}

		return $args;
	}

	/**
	 * Determines if the value is numeric.
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	protected function checkIfValueIsNumeric($value)
	{
		if (is_numeric($value)) {
			return true;
		}

		if (is_array($value)) {
			return count(array_filter($value, 'is_int')) == count($value);
		}

		return false;
	}

	/**
	 * Creates a closure of the meta builder method.
	 *
	 * @return \Closure
	 */
	protected function getMetaBuilderAsClosure()
	{
		return (new \ReflectionMethod($this, 'buildMetaQueryArgument'))->getClosure($this);
	}
}
