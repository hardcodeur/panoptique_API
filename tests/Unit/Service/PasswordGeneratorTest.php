<?php

namespace App\Tests\Unit\Service;

use App\Service\PasswordGenerator;
use PHPUnit\Framework\TestCase;

class PasswordGeneratorTest extends TestCase
{
    private PasswordGenerator $passwordGenerator;

    protected function setUp(): void
    {
        $this->passwordGenerator = new PasswordGenerator();
    }

    public function testGeneratePasswordReturnsStringOfCorrectDefaultLength(): void
    {
        $password = $this->passwordGenerator->generatePassword();

        // On vérifie que la longueur par défaut (12) est bien respectée
        $this->assertEquals(12, strlen($password));
        $this->assertIsString($password);
    }

    public function testGeneratePasswordReturnsStringOfCorrectCustomLength(): void
    {
        $password = $this->passwordGenerator->generatePassword(20);

        // On vérifie qu'une longueur personnalisée (20) est bien respectée
        $this->assertEquals(20, strlen($password));
    }

    public function testPasswordContainsAtLeastOneOfEachRequiredCharacterType(): void
    {
        $password = $this->passwordGenerator->generatePassword(16);

        // On utilise des expressions régulières pour vérifier la présence de chaque type de caractère
        $this->assertMatchesRegularExpression('/[a-z]/', $password, 'Password should contain at least one lowercase letter.');
        $this->assertMatchesRegularExpression('/[A-Z]/', $password, 'Password should contain at least one uppercase letter.');
        $this->assertMatchesRegularExpression('/[0-9]/', $password, 'Password should contain at least one digit.');
        $this->assertMatchesRegularExpression('/[!@#$%^&*()\-_=+{}\[\]|;:,.<>?]/', $password, 'Password should contain at least one symbol.');
    }

    public function testPasswordExcludesSymbolsWhenRequested(): void
    {
        $password = $this->passwordGenerator->generatePassword(16, false);

        // On vérifie que les types de base sont toujours présents
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);

        // Mais on vérifie surtout qu'AUCUN symbole n'est présent
        $this->assertDoesNotMatchRegularExpression('/[!@#$%^&*()\-_=+{}\[\]|;:,.<>?]/', $password, 'Password should not contain any symbols.');
    }

    public function testTwoConsecutivePasswordsAreNotTheSame(): void
    {
        $passwordA = $this->passwordGenerator->generatePassword();
        $passwordB = $this->passwordGenerator->generatePassword();

        // Un test simple pour s'assurer que le générateur ne retourne pas toujours la même chose
        $this->assertNotEquals($passwordA, $passwordB);
    }
}
