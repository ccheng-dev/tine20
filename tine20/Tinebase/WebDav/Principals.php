<?php
/**
 * Tine 2.0
 * 
 * @package     Tinebase
 * @subpackage  WebDav
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @copyright   Copyright (c) 2011-2011 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Lars Kneschke <l.kneschke@metaways.de>
 * 
 */

/**
 * class to handle webdav principals
 * 
 * @package     Tinebase
 * @subpackage  WebDav
 */
class Tinebase_WebDav_Principals implements Sabre_DAVACL_IPrincipalBackend
{
    /**
     * (non-PHPdoc)
     * @see Sabre_DAVACL_IPrincipalBackend::getPrincipalsByPrefix()
     */
    public function getPrincipalsByPrefix($prefixPath) 
    {
        $principals = array();
        
        $principals[] = array(
            'uri'                                   => 'principals/' . Tinebase_Core::getUser()->accountLoginName,
            '{http://sabredav.org/ns}email-address' => Tinebase_Core::getUser()->accountEmailAddress,
            '{DAV:}displayname'                     => Tinebase_Core::getUser()->accountDisplayName
        );

        return $principals;
    }
    
    /**
     * (non-PHPdoc)
     * @see Sabre_DAVACL_IPrincipalBackend::getPrincipalByPath()
     */
    public function getPrincipalByPath($path) 
    {
        return array(
            'id'                                    => Tinebase_Core::getUser()->getId(),
            'uri'                                   => 'principals/' . Tinebase_Core::getUser()->accountLoginName,
            '{http://sabredav.org/ns}email-address' => Tinebase_Core::getUser()->accountEmailAddress,
            '{DAV:}displayname'                     => Tinebase_Core::getUser()->accountDisplayName
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see Sabre_DAVACL_IPrincipalBackend::getGroupMemberSet()
     */
    public function getGroupMemberSet($principal) 
    {
        $result = array();
        
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see Sabre_DAVACL_IPrincipalBackend::getGroupMembership()
     */
    public function getGroupMembership($principal) 
    {
        $result = array();
        
        return $result;
    }
    
    public function setGroupMemberSet($principal, array $members) 
    {
        // do nothing
    }
    
    /**
     * Returns a users' information 
     * 
     * @param  string  $_realm 
     * @param  string  $_username 
     * @return string 
     */
#    public function getUserInfo($_realm, $_username) 
#    {
#        if ($_username == Tinebase_Core::getUser()->accountLoginName) {
#            $userInfo = array(
#                'uri'                                   => 'principals/' . Tinebase_Core::getUser()->accountLoginName,
#                '{http://sabredav.org/ns}email-address' => Tinebase_Core::getUser()->accountEmailAddress,
#                '{DAV:}displayname'                     => Tinebase_Core::getUser()->accountDisplayName
#            ); 
#        } else {
#            array(
#                'uri'               => 'principals/' . $_username,
#            	'{DAV:}displayname' => 'unknown user'
#            );
#        }
#        
#        return $userInfo;
#    }

    /**
     * Returns information about the currently logged in user.
     *
     * If nobody is currently logged in, this method should return null.
     * 
     * @return array|null
     */
    public function getCurrentUser()
    {
        return Tinebase_Core::getUser()->accountLoginName;
    }
    
#    public function getUsers() 
#    {
#        // lis of all users
#        $result = array(
#            Tinebase_Core::getUser()
#        );
#        
#        $rv = array();
#        
#        foreach($result as $user) {
#            $rv[] = array(
#                'uri'                                   => 'principals/' . $user->accountLoginName,
#                '{http://sabredav.org/ns}email-address' => $user->accountEmailAddress,
#                '{DAV:}displayname'                     => $user->accountDisplayName
#            );
#        }
#
#        return $rv;
#
#    }
    
    /**
     * (non-PHPdoc)
     * @see Sabre_DAV_Auth_IBackend::authenticate()
     */
    public function authenticate(Sabre_DAV_Server $_server, $_realm) 
    {
        return true;
    }
}
