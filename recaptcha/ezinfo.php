<?php
/**
 * reCAPTCHA extension for eZ Publish
 * Written by Bruce Morrison <bruce@stuffandcontent.com>
 * Copyright (C) 2008. Bruce Morrison.  All rights reserved.
 * http://www.stuffandcontent.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

class recaptchaInfo
{
    static function info()
    {
        return array(
            'Name' => "reCAPTCHA eZ Publish Integration",
            'Version' => "2.0",
            'Author' => "<a href='http://www.internethic.com'>Internethic</a>",
            'Copyright' => "Copyright (C) 2018-2021 internethic",
            'License' => "GNU General Public License v2.0",
            'Includes the following third-party software' => array( 'Name' => 'PHP Library for reCAPTCHA V2',
                                                                    'Version' => '1.0',
                                                                    'License' => 'GPL - Internethic 2018 - 2021' )
        );
    }
}
?>