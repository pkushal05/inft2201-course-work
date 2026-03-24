<?php
use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Clean and reinitialize the table
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }

    // Test - CreateMail
    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Alice", "Hello world");
        $this->assertIsInt($id);
        $this->assertEquals(1, $id);
    }

    // Test - GetAllMails
    public function testGetAllMails() {
        $mail = new Mail($this->pdo);
        $mail->createMail("Alice", "Hi there");
        $mail->createMail("Bob", "Bye there");

        $mails = $mail->getAllMails();
        $this->assertCount(2, $mails);
        $this->assertEquals("Alice", $mails[0]['subject']);
        $this->assertEquals("Hi there", $mails[0]['body']);
        $this->assertEquals("Bob", $mails[1]['subject']);
        $this->assertEquals("Bye there", $mails[1]['body']);
    }

    // Test - GetMail
    public function testGetMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Kushal", "Hi there");

        $returnedMail = $mail->getMail($id);
        $this->assertCount(1, $returnedMail);
        $this->assertEquals("Kushal", $returnedMail[0]['subject']);
        $this->assertEquals("Hi there", $returnedMail[0]['body']);
    }

    // Test - UpdateMail
    public function testUpdateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Danna", "Hello World");

        $mail->updateMail($id, "John", "Welcome to the World!");

        $returnedMail = $mail->getMail($id);
        $this->assertCount(1, $returnedMail);
        $this->assertEquals("John", $returnedMail[0]['subject']);
        $this->assertEquals("Welcome to the World!", $returnedMail[0]['body']);
    }

    // Test - DeleteMail
    public function testDeleteMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Trump", "Destroy the World");

        $result = $mail->deleteMail($id);
        $this->assertTrue($result);

        $deletedMail = $mail->getMail($id);
        $this->assertCount(0, $deletedMail);
    }

}