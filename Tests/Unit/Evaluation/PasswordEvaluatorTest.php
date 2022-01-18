<?php
namespace SpoonerWeb\BeSecurePw\Tests\Unit\Evaluator;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use SpoonerWeb\BeSecurePw\Evaluation\PasswordEvaluator;
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * Test case.
 *
 * @author Thomas Löffler <loeffler@spooner-web.de>
 */
class PasswordEvaluatorTest extends UnitTestCase
{

    /**
     * @var \SpoonerWeb\BeSecurePw\Evaluation\PasswordEvaluator
     */
    protected $subject = null;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->subject = new PasswordEvaluator();
    }

    /**
     * @test
     * @return void
     */
    public function classCanBeInstantiated()
    {
        static::assertInstanceOf(
            PasswordEvaluator::class,
            $this->subject
        );
    }

    /**
     * @test
     */
    public function returnFieldJavaScriptReturnsDefaultString()
    {
        static::assertEquals(
            'return value;',
            $this->subject->returnFieldJS()
        );
    }

    /**
     * Test for valid passwords.
     * If password is valid, the password will be returned.
     *
     * @test
     * @param array $configuration
     * @param string $password
     * @dataProvider validPasswordDataProvider
     */
    public function checkForValidPassword(array $configuration, string $password)
    {
        $set = true;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_secure_pw'] = $configuration;
        static::assertEquals(
            $password,
            $this->subject->evaluateFieldValue($password, '', $set)
        );
    }

    /**
     * @return array
     */
    public function validPasswordDataProvider()
    {
        return [
            'passwordContainingFourLowerCharactersWithoutConfigurationIsValid' => [
                [],
                'test'
            ],
            'passwordContainingTwelveLowerCharactersWithConfigOfMinimumEightCharactersIsValid' => [
                [
                    'passwordLength' => 8
                ],
                'testpassword'
            ],
            'passwordContainingTwelveLowerCharactersWithConfigOfMinimumEightCharactersAndLowerCharactersIsValid' => [
                [
                    'passwordLength' => 8,
                    'lowercaseChar' => true
                ],
                'testpassword'
            ],
            // @codingStandardsIgnoreLine
            'passwordContainingTwelveUpperAndLowerCharactersWithConfigOfMinimumEightCharactersAndCapitalCharactersIsValid' => [
                [
                    'passwordLength' => 8,
                    'capitalChar' => true,
                    'patterns' => 1
                ],
                'testPassword'
            ],
            // @codingStandardsIgnoreLine
            'passwordContainingTwelveUpperAndLowerCharactersWithConfigOfMinimumEightCharactersDigitsOrCapitalCharactersIsValid' => [
                [
                    'passwordLength' => 8,
                    'capitalChar' => true,
                    'digit' => true,
                    'patterns' => 1
                ],
                'testPassword'
            ],
            // @codingStandardsIgnoreLine
            'passwordContainingUpperLowerDigitsAndSpecialCharactersWith22CharactersWithHardestConfigAndMinimumTwelveCharactersIsValid' => [
                [
                    'passwordLength' => 12,
                    'capitalChar' => true,
                    'lowercaseChar' => true,
                    'digit' => true,
                    'specialChar' => true,
                    'patterns' => 4
                ],
                'Ycb&T8bdHUCP[zD6HqB7pM'
            ]
        ];
    }

    /**
     * Test for invalid passwords.
     * If the password is invalid an empty string will be returned.
     *
     * @test
     * @param array $configuration
     * @param string $password
     * @dataProvider invalidPasswordDataProvider
     */
    public function checkForInvalidPassword(array $configuration, string $password)
    {
        $set = true;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_secure_pw'] = $configuration;
        static::assertEquals(
            '',
            $this->subject->evaluateFieldValue($password, '', $set, false)
        );
    }

    /**
     * @return array
     */
    public function invalidPasswordDataProvider()
    {
        return [
            'emptyPasswordWithoutConfigurationIsInvalid' => [
                [],
                ''
            ],
            'passwordContainingFourLowerCharactersWithConfigOfMinimumEightCharactersIsInvalid' => [
                [
                    'passwordLength' => 8
                ],
                'test'
            ],
            // @codingStandardsIgnoreLine
            'passwordContainingTwelveLowerCharactersWithConfigOfMinimumEightCharactersAndCapitalCharactersIsInvalid' => [
                [
                    'passwordLength' => 8,
                    'capitalChar' => true,
                    'patterns' => 1
                ],
                'testpassword'
            ],
            // @codingStandardsIgnoreLine
            'passwordContainingTwelveUpperAndLowerCharactersWithConfigOfMinimumEightCharactersDigitsAndCapitalCharactersIsInvalid' => [
                [
                    'passwordLength' => 8,
                    'capitalChar' => true,
                    'digit' => true,
                    'patterns' => 2
                ],
                'testPassword'
            ],
        ];
    }
}
