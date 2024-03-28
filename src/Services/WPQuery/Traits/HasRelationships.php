<?php

declare(strict_types=1);

namespace Yard\QueryBlock\Services\WPQuery\Traits;

use Fusion\Database\P2PRelationRegistrar;

trait HasRelationships
{

	/**
	 * Array of all the set relationships.
	 *
	 * @var array
	 */
	protected $relations = [];

	/**
	 * Define an inverse one-to-one relationship.
	 *
	 * @param $posttype
	 * @param $reciprocal
	 * @param $settings
	 *
	 * @return array
	 */
	public function connect($posttype, $reciprocal = false, $settings = [])
	{
		$relation = [
			'name' => $this->getRelationName($posttype),
			'from' => $this->getName(),
			'to' => $this->getNameOfOtherPostType($posttype),
			'reciprocal' => $reciprocal,
		];

		$this->relations[] = array_merge($relation, $settings);
	}

	/**
	 * Get all relations of the post type.
	 *
	 * @return array
	 */
	public function getRelationships()
	{
		return $this->relations;
	}

	/**
	 * Get the name of a relation.
	 *
	 * @param $posttype
	 *
	 * @return string
	 */
	protected function getRelationName($posttype)
	{
		return $this->getName().'_to_'.$this->getNameOfOtherPostType($posttype);
	}

	/**
	 * Registers all relations with the RelationRegistratar
	 */
	protected function registerRelations()
	{
		(new P2PRelationRegistrar($this->getRelationships()))->register();
	}

	/**
	 * Gets the name of another post type.
	 *
	 * @param $posttype
	 */
	protected function getNameOfOtherPostType($posttype)
	{
		return (new \ReflectionClass($posttype))->getDefaultProperties()['name'];
	}
}
