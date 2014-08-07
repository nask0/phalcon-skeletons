<?php
namespace Models\Entities;

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class User extends \Phalcon\Mvc\Model
{
    const ACTION_NO = 0;
    const ACTION_YES = 1;

    /**
     * @db
     * @var integer
     */
    protected $id;

    /**
     * @db
     * @var string
     */
    protected $email;
     
    /**
     * @db
     * @var string
     */
    protected $nickname;

    /**
     * @db
     * @var string
     */
    protected $first_name;

    /**
     * @db
     * @var string
     */
    protected $last_name;
     
    /**
     * @db
     * @var string
     */
    protected $password;

    /**
     * @db
     * @var int 0/1
     */
    protected $banned;

    /**
     * @db
     * @var int 0/1
     */
    protected $deleted = 0;
     
    /**
     * @db
     * @var string
     */
    protected $created;

    public function initialize()
    {
        $this->setSource('users');

        /**
         * SQL UPDATE statements are by default created with every column defined in the model
         * (full all-field SQL update). You can change specific models to make dynamic updates,
         * in this case, just the fields that had changed are used to create the final SQL statement.
         * In some cases this could improve the performance by reducing the traffic between the application
         * and the database server, this specially helps when the table has blob/text fields
         */
        $this->useDynamicUpdate(true);

        // Skips fields/columns on both INSERT/UPDATE operations
        // $this->skipAttributes(array('updated'));

        // Skips only when inserting
        $this->skipAttributesOnCreate(array('id', 'created'));

        // Skips only when updating
        // $this->skipAttributesOnUpdate(array(''));

        /*$this->addBehavior(
            new SoftDelete(array(
                'field' => 'deleted',
                'value' => '1'
            ))
        );*/
    }

    /**
     * Method to set the value of field iduser
     *
     * @param integer $iduser
     * @return $this
     */
    public function setId($userId)
    {
        $this->$userId = (int) $userId;
        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setNickname($nickname)
    {
        $this->$nickname = (string) $nickname;
        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Method to set the value of field datetime
     *
     * @param string $datetime
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = (string) $created;
        return $this;
    }

    /**
     * Returns the value of field iduser
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setDeleted($del)
    {
        $this->deleted = (int) $del;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Returns the value of field created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $this->validate(
            new Email(
                array(
                    'field' => 'email',
                    'required' => true
                )
            )
        );

        return ($this->validationHasFailed() == true);
    }
}