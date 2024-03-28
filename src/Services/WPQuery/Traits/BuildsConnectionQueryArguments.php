<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services\WPQuery\Traits;

trait BuildsConnectionQueryArguments
{
	/**
	 * Builds a connection query arguments array.
	 *
	 * @param string       $from
	 * @param string|array $to
	 * @param array        $meta
	 * @param object       $object
	 *
	 * @return array
	 */
	protected function buildConnectionQueryArgument($from, $to = null, $meta = [], $object = null)
	{
		// If there is only one value passed to the method we assume it's
		// the actual name of the connection. If it has two arguments, but
		// "to" is an array, we assume that to is the connection metadata.
		if (1 == func_num_args() || is_array($to)) {
			$type = $from;
			$meta = $to;
		}

		return [
			'connected_type' => isset($type) ? $type : $from.'_to_'.$to,
			'connected_items' => $this->getConnectionObject($object),
			'connected_meta' => $meta,
			'nopaging' => true,
			'suppress_filters' => false,
		];
	}

	/**
	 * Gets the connection object.
	 *
	 * @param $object
	 *
	 * @return \WP_Post
	 */
	protected function getConnectionObject($object = null)
	{
		if (is_null($object)) {
			return get_queried_object();
		}

		if ($object instanceof \WP_Post) {
			return $object;
		}

		return get_post($object);
	}
}
