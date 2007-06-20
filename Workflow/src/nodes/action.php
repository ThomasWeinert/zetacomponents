<?php
/**
 * File containing the ezcWorkflowNodeAction class.
 *
 * @package Workflow
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeAction class represents an activity node holding business logic.
 *
 * When the node is reached during execution of the workflow, the business logic that is implemented
 * by the associated service object is executed.
 *
 * Service objects can return true to resume execution of the
 * workflow or false to suspend the workflow (unless there are other active nodes)
 * and be re-executed later
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * The following example displays how to create a workflow with a very
 * simple service object that prints the argument it was given to the
 * constructor:
 * <code>
 * class MyPrintAction implements ezcWorkflowServiceObject
 * {
 *     private $whatToSay;
 *
 *     public function __construct( $whatToSay )
 *     {
 *         $this->whatToSay = $whatToSay;
 *     }
 *
 *     public function execute()
 *     {
 *         print $this->whatToSay;
 *         return true; // we're finished, activate next node
 *     }
 * }
 *
 * $workflow = new ezcWorkflow( 'Test' );
 *
 * $action = new ezcWorkflowNodeAction( array( "class" => "MyPrintAction",
 *                                             "arguments" => "No. 1 The larch!" ) );
 * $action->addOutNode( $workflow->endNode );
 * $workflow->startNode->addOutNode( $action );
 * </code>
 *
 * @package Workflow
 * @version //autogen//
 */
class ezcWorkflowNodeAction extends ezcWorkflowNode
{
    /**
     * Constructs a new action node with the configuration $configuration.
     *
     * Configuration format
     * <ul>
     * <li>
     *   <b>String:</b>
     *   The class name of the service object. Must implement ezcWorkflowServiceObject. No
     *   arguments are passed to the constructor.
     * </li>
     *
     * <li>
     *   <b>Array:</b>
     *   <ul>
     *     <li><i>class:</i> The class name of the service object. Must implement ezcWorkflowServiceObject.</li>
     *     <li><i>arguments:</i> Array of values that are passed to the constructor of the service object.</li>
     *   </ul>
     * <li>
     * </ul>
     *
     * @param mixed $configuration
     * @throws ezcWorkflowDefinitionStorageException
     */
    public function __construct( $configuration )
    {
        if ( is_string( $configuration ) )
        {
            $configuration = array( 'class' => $configuration );
        }

        if ( !isset( $configuration['arguments'] ) )
        {
            $configuration['arguments'] = array();
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this node by creating the service object and calling its execute() method.
     *
     * If the service object returns true, the output node will be activated.
     * If the service node returns false the workflow will be suspended
     * unless there are other activated nodes. An action node suspended this way
     * will be executed again the next time the workflow is resumed.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        $object   = $this->createObject();
        $finished = $object->execute( $execution );

        // Execution of the Service Object has finished.
        if ( $finished !== false )
        {
            $this->activateNode( $execution, $this->outNodes[0] );

            return parent::execute( $execution );
        }
        // Execution of the Service Object has not finished.
        else
        {
            return false;
        }
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        try
        {
            $object = $this->createObject();
        }
        catch ( ezcBaseAutoloadException $e )
        {
            return 'Class not found.';
        }
        catch ( ezcWorkflowExecutionException $e )
        {
            return $e->getMessage();
        }

        return (string)$object;
    }

    /**
     * Returns the service object as specified by the configuration.
     *
     * @return ezcWorkflowServiceObject
     */
    protected function createObject()
    {
        if ( !class_exists( $this->configuration['class'] ) )
        {
            throw new ezcWorkflowExecutionException(
              'Class not found.'
            );
        }

        $class = new ReflectionClass( $this->configuration['class'] );

        if ( !$class->implementsInterface( 'ezcWorkflowServiceObject' ) )
        {
            throw new ezcWorkflowExecutionException(
              'Class does not implement the ezcWorkflowServiceObject interface.'
            );
        }

        if ( !empty( $this->configuration['arguments'] ) )
        {
            return $class->newInstanceArgs( $this->configuration['arguments'] );
        }
        else
        {
            return $class->newInstance();
        }
    }
}
?>
