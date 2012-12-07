<?php
namespace Ldap;
/**
 * Holds an LDAP search result and provides array access and iterator functionality to traverse the entries.
 */
class Result extends Base implements \ArrayAccess, \Iterator
{
    /**
     * @var array the LDAP entries.
     */
    private $_entries;

    /**
     * @var int the number of LDAP entries.
     */
    private $_count;

    /**
     * Points to the current LDAP entry. Used by the iterator implementation.
     * @var int pointer to the current LDAP entry.
     */
    private $_entryPointer;

    /**
     * Constructor.
     * @param $conn resource the LDAP link identifier.
     * @param $result resource the LDAP result identifier.
     */
    public function __construct($conn, $result)
	{
		$this->_setEntryPointer(0);
		$this->_setLink($conn);
		$this->_setResult($result);
		$this->_setEntries(ldap_get_entries($this->_getLink(), $this->_getResult()));
	}

	// ArrayAccess implementations 

	public function offsetExists ($offset)
	{
		return $this->_getEntry($offset) !== 0;
	}

	public function offsetGet ($offset)
	{
		return $this->_getEntry($offset);
	}

	public function offsetSet ($offset, $value)
	{
		throw new Exception('Result is read-only!');
	}

	public function offsetUnset ($offset)
	{
		throw new Exception('Result is read-only!');
	}

	// Iterator implementations

	public function count()
	{
        if (!isset($this->_count))
        {
            $this->_setCount(ldap_count_entries($this->_getLink(), $this->_getResult()));
        }
        return $this->_count;
	}

	public function current()
	{
		return $this->_getEntry($this->_getEntryPointer());
	}

	public function key()
	{
		return $this->_getEntryPointer();
	}

	public function next()
	{
		$this->_incrementEntryPointer();
	}

	public function valid()
	{
		$currentEntry = $this->_getCurrentEntry();
		return isset($currentEntry);
	}

	public function rewind()
	{
		$this->_setEntryPointer(0);
	}

    /**
     * Returns an LDAP entry at the given index.
     * @param $index int the index at which to retrieve the LDAP entry.
     * @return null|array the LDAP entry if it exists, otherwise null.
     */
    private function _getEntry($index)
	{
		return isset($this->_entries[$index])
			? $this->_entries[$index]
			: NULL;
	}

    /**
     * Returns the current LDAP entry, determined by the @link{$entryPointer} field.
     * @return array|null the LDAP entry if it exists, otherwise null.
     */
    private function _getCurrentEntry()
	{
		return $this->_getEntry($this->_getEntryPointer());
	}

    /**
     * Sets the LDAP entries.
     * @param $entries array the LDAP entries.
     */
    private function _setEntries($entries)
	{
		$this->_entries = $entries;
	}

    /**
     * Sets the entry count.
     * @param $count int the entry count.
     */
    private function _setCount($count)
	{
		$this->_count = $count;
	}

    /**
     * Sets the entry pointer index.
     * @param $index int the entry pointer index.
     */
    private function _setEntryPointer($index)
	{
		$this->_entryPointer = $index;
	}

    /**
     * Returns the entry pointer index.
     * @return int the entry pointer index.
     */
    private function _getEntryPointer()
	{
		return $this->_entryPointer;
	}

    /**
     * Increments the entry pointer index by 1.
     */
    private function _incrementEntryPointer()
	{
		$this->_setEntryPointer($this->_getEntryPointer() + 1);
	}
}