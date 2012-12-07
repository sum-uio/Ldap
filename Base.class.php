<?php
namespace Ldap;
/**
 * Holds LDAP link and result identifiers, used by @link{LdapManager} and @link{LdapResult}.
 */
abstract class Base {

    /**
     * @var resource the LDAP link identifier.
     */
    private $_link;

    /**
     * @var resource the LDAP result identifier.
     */
    private $_result;

    /**
     * Sets the LDAP link identifier.
     * @param $link resource the LDAP link identifier.
     */
    protected function _setLink($link)
	{
        if (is_resource($link))
		{
			$this->_link = $link;
		}
	}

    /**
     * Returns the LDAP link identifier.
     * @return resource the LDAP link identifier.
     */
    protected function _getLink()
	{
		return $this->_link;
	}

    /**
     * Sets the LDAP result identifier.
     * @return resource the LDAP result identifier.
     */
    protected function _getResult()
	{
		return $this->_result;
	}

    /**
     * Returns the LDAP result identifier.
     * @param $result resource the LDAP result identifier.
     */
    protected function _setResult($result)
	{
        if (is_resource($result))
        {
            $this->_result = $result;
        }
	}
}
