<?php

/*
 * This file is part of the ProjectA AclBundle.
 *
 * (c) Project A Ventures GmbH & Co. KG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ProjectA\Bundle\AclBundle\Tests\Security\Acl\Manager\AceManager;

use ProjectA\Bundle\AclBundle\Tests\Model\AbstractSecurityTest;
use Symfony\Component\Security\Acl\Dbal\Schema;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ObjectAceManagerTest extends AbstractSecurityTest
{
    public function testGrantSingleMask()
    {
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_EDIT, $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object));
    }

    public function testGrantMultipleMask()
    {
        $maskBuilder = new MaskBuilder();
        $mask = $maskBuilder->add(MaskBuilder::MASK_VIEW)->add(MaskBuilder::MASK_DELETE);

        $this->objectmanager->grant($this->object, $mask->get(), $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object));
        $this->assertTrue($this->manager->isGranted('DELETE', $this->object));
    }

    public function testOverwrite()
    {
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_EDIT, $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));
        $this->assertTrue($this->manager->isGranted('EDIT', $this->object));

        $this->objectmanager->overwrite($this->object, MaskBuilder::MASK_VIEW, $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));
        $this->assertFalse($this->manager->isGranted('EDIT', $this->object));
    }

    public function testRevoke()
    {
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_VIEW, $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));

        $this->objectmanager->revoke($this->object, MaskBuilder::MASK_VIEW, $this->token);

        $this->assertFalse($this->manager->isGranted('VIEW', $this->object));
    }

    public function testRevokeAllForIdentityForSingleGrant()
    {
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_VIEW, $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));

        $this->objectmanager->revokeAllForIdentity($this->object, $this->token);

        $this->assertFalse($this->manager->isGranted('VIEW', $this->object));
    }

    public function testRevokeAllForIdentityForMultiGrant()
    {
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_VIEW, $this->token);
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_MASTER, $this->token);
        $this->objectmanager->grant($this->object, MaskBuilder::MASK_OWNER, $this->token);

        $this->assertTrue($this->manager->isGranted('VIEW', $this->object));
        $this->assertTrue($this->manager->isGranted('MASTER', $this->object));
        $this->assertTrue($this->manager->isGranted('OWNER', $this->object));

        $this->objectmanager->revokeAllForIdentity($this->object, $this->token);

        $this->assertFalse($this->manager->isGranted('VIEW', $this->object));
        $this->assertFalse($this->manager->isGranted('MASTER', $this->object));
        $this->assertFalse($this->manager->isGranted('OWNER', $this->object));
    }
}