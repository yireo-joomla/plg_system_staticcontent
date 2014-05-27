<?php
/**
 * Joomla! plugin StaticContent
 *
 * @author Yireo (info@yireo.com)
 * @copyright Copyright 2014 Yireo.com. All rights reserved
 * @license GNU Public License
 * @link http://www.yireo.com/
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the parent class
jimport( 'joomla.plugin.plugin' );

/**
 * Plugin class for StaticContent
 */
class plgSystemStaticContent extends JPlugin
{
    /**
     * Load the parameters
     * 
     * @access private
     * @param null
     * @return JParameter
     */
    private function getParams()
    {
        return $this->params;
    }

    /*
     * Method to display the requested view
     *
     * @param null
     * @return null
     */
	public function onAfterRender()
	{
        // Get system variables
        $application = JFactory::getApplication();
        $template = $application->getTemplate();

        // Only continue in the frontend
        if(!$application->isSite()) {
            return false;
        }

        // Get the URL from the parameters
        $uri = JURI::current();
        if(preg_match('/^https/', $uri)) {
            $url = $this->getParams()->get('secure_url');
        } else {
            $url = $this->getParams()->get('unsecure_url');
        }
        $url = preg_replace('/\/$/', '', trim($url));
        if(empty($url)) {
            return false;
        }

        // Initialize the folders
        $folders = array();

        // Add the /includes folder
        if((int)$this->getParams()->get('use_includes') == 1) {
            $folders[] = 'includes/';
        }

        // Add the /cache folder
        if((int)$this->getParams()->get('use_cache') == 1) {
            $folders[] = 'cache/';
        }

        // Add the /images folder
        if((int)$this->getParams()->get('use_images') == 1) {
            $folders[] = 'images/';
        }

        // Add the /media folder
        if((int)$this->getParams()->get('use_media') == 1) {
            $folders[] = 'media/';
        }

        // Add the templates images-folder
        if((int)$this->getParams()->get('use_template_images') == 1) {
            $folders[] = 'templates/'.$template.'/images/';
        }
        
        // Add the templates CSS-folder
        if((int)$this->getParams()->get('use_template_css') == 1) {
            if(is_dir(JPATH_SITE.'/templates/'.$template.'/css')) {
                $folders[] = 'templates/'.$template.'/css/';
            }
        }

        // Add the templates JavaScript-folder
        if((int)$this->getParams()->get('use_template_js') == 1) {
            if(is_dir(JPATH_SITE.'/templates/'.$template.'/js')) {
                $folders[] = 'templates/'.$template.'/js/';
            }
            if(is_dir(JPATH_SITE.'/templates/'.$template.'/javascript')) {
                $folders[] = 'templates/'.$template.'/javascript/';
            }
        }

        // Get the body
        $body = JResponse::getBody();
        foreach($folders as $folder) {
            $remote = $url.'/'.$folder;

            $body = str_replace('url('.$folder, 'url('.$remote, $body);
            $body = str_replace('url(/'.$folder, 'url('.$remote, $body);
            $body = str_replace('url(\''.$folder, 'url(\''.$remote, $body);
            $body = str_replace('url(\'/'.$folder, 'url(\''.$remote, $body);
            $body = str_replace('url("'.$folder, 'url("'.$remote, $body);
            $body = str_replace('url("/'.$folder, 'url("'.$remote, $body);
            $body = str_replace('"'.$folder, '"'.$remote, $body);
            $body = str_replace('\''.$folder, '\''.$remote, $body);
            $body = str_replace('"/'.$folder, '"'.$remote, $body);
            $body = str_replace('\'/'.$folder, '\''.$remote, $body);
            $body = str_replace(JURI::base().$folder, $remote, $body);
        }
        JResponse::setBody($body);
    }
}
