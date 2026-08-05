<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Transactional
{
    /**
     * Determines if a security check is required before starting the transaction.
     */
    public bool $secure;

    /**
     * If a security check is enabled, this property can define the required role.
     */
    public ?string $requiredRole;

    /**
     * Optional description for the transaction.
     */
    public ?string $description;

    /**
     * Constructor for the Transactional attribute.
     *
     * @param bool $secure Whether to enforce a security check before starting the transaction.
     * @param string|null $requiredRole The required role for executing the transactional method.
     * @param string|null $description Optional description or context.
     */
    public function __construct(bool $secure = true, ?string $requiredRole = null, ?string $description = null)
    {
        $this->secure = $secure;
        $this->requiredRole = $requiredRole;
        $this->description = $description;
    }
}
