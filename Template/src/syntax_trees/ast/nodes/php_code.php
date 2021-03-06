<?php
/**
 * File containing the ezcTemplatePHPCodeAstNode class
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package Template
 * @version //autogen//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @access private
 */
/**
 * Contains a node with raw PHP code.
 *
 * @package Template
 * @version //autogen//
 * @access private
 */
class ezcTemplatePhpCodeAstNode extends ezcTemplateStatementAstNode
{
    /**
     * Code that needs to be output.
     *
     * @var string
     */
    public $code;

    /**
     * Constructs a new php code node.
     *
     * @param string $code
     */
    public function __construct( $code = null )
    {
        parent::__construct();
        $this->code = $code;
    }
}
?>
