<?php

namespace li3_topsy\extensions\adapter\data\source\http;

use lithium\data\model\QueryException;
use lithium\util\String;

class Topsy extends \lithium\data\source\Http {

	/**
	 * Class dependencies.
	 */
	protected $_classes = array(
		'service' => 'lithium\net\http\Service',
		'entity' => 'lithium\data\entity\Document',
		'set' => 'lithium\data\collection\DocumentSet'
	);

	/**
	 * Exposed resources, with required and optional params
	 *
	 * @var array
	 * @link http://code.google.com/p/otterapi/wiki/Resources
	 */
	protected $_resources = array(
		'authorinfo' => array(
			'path' => '/authorinfo.{:type}{:params}',
			'params' => array(
				'required' => 'url'
			)
		),
		'experts' => array(
			'path' => '/experts.{:type}{:params}',
			'params' => array(
				'required' => 'q',
				'optional' => 'config_NoFilters'
			)
		),
		'linkposts' => array(
			'path' => '/linkposts.{:type}{:params}',
			'params' => array(
				'required' => 'url',
				'optional' => array('contains', 'tracktype')
			)
		),
		'linkpostcount' => array(
			'path' => '/linkpostcount.{:type}{:params}',
			'params' => array(
				'required' => 'url',
				'optional' => array('contains', 'tracktype')
			)
		),
		'populartrackbacks' => array(
			'path' => '/populartrackbacks.{:type}{:params}',
			'params' => array(
				'required' => 'url'
			)
		),
		'search' => array(
			'path' => '/search.{:type}{:params}',
			'params' => array(
				'required' => 'q',
				'optional' => array('window', 'type', 'query_features')
			)
		),
		'searchcount' => array(
			'path' => '/searchcount.{:type}{:params}',
			'params' => array(
				'required' => 'q',
				'optional' => 'dynamic'
			)
		),
		'searchhistogram' => array(
			'path' => '/searchhistogram.{:type}{:params}',
			'params' => array(
				'required' => 'q',
				'optional' => array('slice', 'period', 'count_method')
			)
		),
		'searchdate' => array(
			'path' => '/searchdate.{:type}{:params}',
			'params' => array(
				'required' => 'q',
				'optional' => array('window', 'type', 'zoom')
			)
		),
		'stats' => array(
			'path' => '/stats.{:type}{:params}',
			'params' => array(
				'required' => 'url',
				'optional' => 'contains'
			)
		),
		'top' => array(
			'path' => '/top.{:type}{:params}',
			'params' => array(
				'required' => 'thresh',
				'optional' => array('type', 'locale')
			)
		),
		'tags' => array(
			'path' => '/tags.{:type}{:params}',
			'params' => array(
				'required' => 'url'
			)
		),
		'trackbacks' => array(
			'path' => '/trackbacks.{:type}{:params}',
			'params' => array(
				'required' => 'url',
				'optional' => array('contains', 'infonly', 'sort_method')
			)
		),
		'trending' => array(
			'path' => '/trending.{:type}{:params}',
			'params' => array()
		),
		'urlinfo' => array(
			'path' => '/urlinfo.{:type}{:params}',
			'params' => array(
				'required' => 'url'
			)
		)
	);

	/**
	 * List params available to all resources.
	 *
	 * - 'page' Page number of the result set. (default: 1, max: 10)
	 * - 'perpage' Limit number of results per page. (default: 10, max: 100)
	 * - 'offset' Offset from which to start the results,
	 *     should be set to last_offset parameter returned in the previous page.
	 * - 'mintime' Earliest date/time to restrict a result set. unix-timestamp format.
	 * - 'maxtime' Most recent date/time to restrict a result set. unix-timestamp format.
	 * - 'nohidden' Toggles hiding of duplicate results. default is 1, which means no results are hidden.
	 *     nohidden=0 will return unique results only.
	 * - 'allow_lang' Language filter which lets you specify the languages you would like to see results in.
	 *     Currently supports ja, zh, ko and en. Option also takes a comma separated list of languages.
	 * - 'family_filter' Filters all content containing profanity. Should be set to 1
	 *
	 * @var array
	 * @link http://code.google.com/p/otterapi/wiki/ResListParameters
	 */
	protected $_params = array(
		'page', 'perpage', 'offset', 'mintime', 'maxtime', 'nohidden', 'allow_lang', 'family_filter'
	);

	public function __construct(array $config = array()) {
		$defaults = array(
			'scheme'   => 'http',
			'host'     => 'otter.topsy.com',
			'port'     => 80,
			'version'  => '1.1'
		);
		$config += $defaults;

		parent::__construct($config + $defaults);
	}

	/**
	 * Returns available resources.
	 *
	 * @param object $class
	 * @return array
	 */
	public function sources($class = null) {
		return array_keys($this->_resources);
	}

	/**
	 * Describe data source.
	 *
	 * @param string $entity
	 * @param array $meta
	 * @return array - returns an empty array
	 */
	public function describe($entity, array $meta = array()) {
		return array();
	}

	/**
	 * Data source READ operation.
	 *
	 * @param string $query
	 * @param array $options
	 * @return mixed
	 */
	public function read($query, array $options = array()) {
		extract($query->export($this, array('keys' => array('source', 'conditions'))));

		if (!$path = $this->_path($source, $conditions)) {
			return null;
		}

		$result = json_decode($this->connection->get($path), true);

		if (empty($result['response'])) {
			return null;
		}

		if (isset($result['response']['errors'])) {
			throw new QueryException($result['response']['error']);
		}

		return $this->item($query->model(), $result['response'], array('class' => 'set'));
	}


	/**
	 * Used for object formatting.
	 *
	 * @param string $entity
	 * @param array $data
	 * @param array $options
	 * @return mixed
	 */
	public function cast($entity, array $data, array $options = array()) {
		foreach ($data as $key => $val) {
			if (!is_array($val)) {
				continue;
			}
			$data[$key] = $this->item($entity->model(), $val, array('class' => 'entity'));
		}
		return parent::cast($entity, $data, $options);
	}

	/**
	 * Convert conditions to a path
	 *
	 * @param string $source
	 * @param array $conditions
	 * @return string
	 */
	protected function _path($source, array $conditions = array()) {
		if (!isset($this->_resources[$source])) {
			return null;
		}

		$required = (array) $this->_resources[$source]['params']['required'];
		if (array_intersect($required, array_flip($conditions)) != $required) {
			throw new QueryException('Parameters incomplete');
		}

 		$params = '?' . http_build_query($conditions);

		$path = String::insert($this->_resources[$source]['path'], compact('params') + array('type' => 'json'));

		return $path;
	}

	public function create($query, array $options = array()) {
		throw new QueryException('The Otter REST API is currently read only.');
	}

	public function update($query, array $options = array()) {
		throw new QueryException('The Otter REST API is currently read only.');
	}

	public function delete($query, array $options = array()) {
		throw new QueryException('The Otter REST API is currently read only.');
	}
}

?>