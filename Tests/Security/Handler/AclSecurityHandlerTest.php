<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\AdminBundle\Tests\Admin\Security\Acl\Permission;

use Sonata\AdminBundle\Security\Handler\AclSecurityHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class AclSecurityHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testAcl()
    {
        $admin = $this->getMock('Sonata\AdminBundle\Admin\AdminInterface');
        $admin->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('test'));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));

        $handler = new AclSecurityHandler($securityContext, array());

        $this->assertTrue($handler->isGranted($admin, array('TOTO')));
        $this->assertTrue($handler->isGranted($admin, 'TOTO'));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(false));

        $handler = new AclSecurityHandler($securityContext, array());

        $this->assertFalse($handler->isGranted($admin, array('TOTO')));
        $this->assertFalse($handler->isGranted($admin, 'TOTO'));
    }

    public function testBuildInformation()
    {
        $informations = array(
            'EDIT' => array('EDIT')
        );

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $admin = $this->getMock('Sonata\AdminBundle\Admin\AdminInterface');
        $admin->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('test'));

        $admin->expects($this->once())
            ->method('getSecurityInformation')
            ->will($this->returnValue($informations));

        $handler = new AclSecurityHandler($securityContext, array());

        $results = $handler->buildSecurityInformation($admin);

        $this->assertArrayHasKey('ROLE_TEST_EDIT', $results);
    }

    public function testWithAuthenticationCredentialsNotFoundException()
    {
        $admin = $this->getMock('Sonata\AdminBundle\Admin\AdminInterface');
        $admin->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('test'));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->any())
            ->method('isGranted')
            ->will($this->throwException(new AuthenticationCredentialsNotFoundException('FAIL')));

        $handler = new AclSecurityHandler($securityContext, array());

        $this->assertFalse($handler->isGranted($admin, 'raise exception', $admin));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testWithNonAuthenticationCredentialsNotFoundException()
    {
        $admin = $this->getMock('Sonata\AdminBundle\Admin\AdminInterface');
        $admin->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('test'));

        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContext->expects($this->any())
            ->method('isGranted')
            ->will($this->throwException(new \RunTimeException('FAIL')));

        $handler = new AclSecurityHandler($securityContext, array());

        $this->assertFalse($handler->isGranted($admin, 'raise exception', $admin));
    }
}