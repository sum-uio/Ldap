<?php
namespace Ldap;
/**
 * Manages LDAP connection and querying.
 */
class Manager extends Base
{
    /**
     * @var bool Holds the LDAP connection state.
     */
    private $_isOpen;

    /**
     * Constructor. Opens a connection to an LDAP host.
     * @param $host string the LDAP host.
     * @throws Exception If connection to the LDAP host is unsuccessful.
     */
    public function __construct($host)
	{
		$this->_setIsOpen(false);
		if (!$this->_connect($host))
		{
			throw new Exception('Could not connect to LDAP server.');
		}
        if (!$this->_bind()) {
            throw new Exception('Could not bind to LDAP directory.');
        }
		$this->_setIsOpen(true);
	}

    /**
     * Closes the LDAP connection if it's still open.
     */
    public function __destruct()
	{
		if ($this->_getIsOpen())
		{
			$this->close();
		}
	}

    /**
     * Opens a connection to an LDAP host and returns the LDAP connection resource.
     * @param $host string the LDAP host.
     * @return bool|resource the LDAP connection resource if connection is successful, otherwise false.
     */
    private function _connect($host)
	{
		$this->_setLink(ldap_connect($host));
        return $this->_getLink();
	}

    /**
     * Binds to an LDAP directory.
     * @param null $bindRDN string the relative DN to which to bind.
     * @param null $bindPassword the
     * @return bool true if binding to the LDAP directory is successful, otherwise false.
     */
    private function _bind($bindRDN = null, $bindPassword = null)
	{
		return ldap_bind($this->_getLink(), $bindRDN, $bindPassword);
	}

    /**
     * Closes the connection to the LDAP host.
     */
    public function close()
	{
		ldap_close($this->_getLink());
	}

    /**
     * Searches after entries in a given directory.
     * @param $baseDN string the base DN for the directory.
     * @param $filter string the filter with which to search in the LDAP directory. Putting unprocessed filter
     * arguments are discouraged - supply arguments in the $filterArgs parameter instead.
     * @param array $filterArgs array the filter arguments as an array, with replace tokens as the key. I.e.,
     * array(':foo' => 'bar') will replace occurrences of ':foo' with the text 'bar' in the $filter argument.
     * @param array $attributes array the entry attributes to fetch from the query.
     * @return bool|Result the result of the LDAP query on success, otherwise false.
     */
    public function search($baseDN, $filter, $filterArgs = array(), $attributes = array())
	{
		$filteredSearch = self::_replaceFilterArgs($filter, $filterArgs);
		$result = ldap_search($this->_getLink(), $baseDN, $filteredSearch, $attributes);
		if ($result)
		{
			$this->_setResult($result);
            return $this->_createResult();
		}
        return false;
	}

    /**
     * Replaces parameter tokens with filter arguments (for instance, array(':foo' => 'bar') will replace occurrences
     * of ':foo' with the text 'bar'.
     * @param $filter string the unprocessed filter.
     * @param $filterArgs array the filter arguments.
     * @return string the processed filter.
     */
    private static function _replaceFilterArgs($filter, $filterArgs)
	{
		$filteredSearch = $filter;
		foreach ($filterArgs as $paramKey => $paramValue)
		{
			$filteredParamValue = preg_replace('#[^\\w *]#', '', $paramValue);
			$filteredSearch = preg_replace('%' . $paramKey . '%', $filteredParamValue, $filteredSearch);
		}
		return $filteredSearch;
	}

    /**
     * Wraps the LDAP result resource in an LDAPResult object.
     * @return Result the LDAP result.
     */
    private function _createResult()
	{
		return new Result($this->_getLink(), $this->_getResult());
	}

    /**
     * Sets the LDAP connection state.
     * @param $isOpen bool the LDAP connection state - true if open, otherwise false.
     */
    private function _setIsOpen($isOpen)
    {
        $this->_isOpen = $isOpen;
    }

    /**
     * Returns the LDAP connection state.
     * @return bool the LDAP connection state - true if open, otherwise false.
     */
    private function _getIsOpen()
    {
        return $this->_isOpen;
    }
}