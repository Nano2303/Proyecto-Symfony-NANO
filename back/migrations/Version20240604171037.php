<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604171037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carrito_compras (id INT AUTO_INCREMENT NOT NULL, usuarios_id INT NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_F782880FF01D3B25 (usuarios_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorias (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(20) NOT NULL, descripcion VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detalles_orden (id INT AUTO_INCREMENT NOT NULL, ordenes_id INT NOT NULL, productos_id INT NOT NULL, cantidad INT NOT NULL, precio_unitario DOUBLE PRECISION NOT NULL, INDEX IDX_FD5930413BA949C (ordenes_id), INDEX IDX_FD59304ED07566B (productos_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE direcciones (id INT AUTO_INCREMENT NOT NULL, usuarios_id INT NOT NULL, calle VARCHAR(100) NOT NULL, ciudad VARCHAR(50) NOT NULL, provincia VARCHAR(100) NOT NULL, codigo_postal VARCHAR(10) NOT NULL, pais VARCHAR(20) NOT NULL, INDEX IDX_B0B0BECBF01D3B25 (usuarios_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordenes (id INT AUTO_INCREMENT NOT NULL, usuarios_id INT NOT NULL, pagos_id INT NOT NULL, fecha VARCHAR(20) NOT NULL, total DOUBLE PRECISION NOT NULL, direccion_envio VARCHAR(255) NOT NULL, estado VARCHAR(10) NOT NULL, INDEX IDX_55FDC04AF01D3B25 (usuarios_id), UNIQUE INDEX UNIQ_55FDC04AAAAC586 (pagos_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pagos (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, monto DOUBLE PRECISION NOT NULL, metodo_pago VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE productos (id INT AUTO_INCREMENT NOT NULL, categorias_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) NOT NULL, precio DOUBLE PRECISION NOT NULL, talla VARCHAR(5) NOT NULL, color VARCHAR(20) NOT NULL, cantidad_inventario INT NOT NULL, src LONGTEXT NOT NULL, INDEX IDX_767490E65792B277 (categorias_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE productos_carrito (id INT AUTO_INCREMENT NOT NULL, carrito_compras_id INT NOT NULL, productos_id INT NOT NULL, cantidad INT NOT NULL, INDEX IDX_4FF85FCA9DF172B5 (carrito_compras_id), INDEX IDX_4FF85FCAED07566B (productos_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usuarios (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, telefono VARCHAR(15) NOT NULL, nombre VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carrito_compras ADD CONSTRAINT FK_F782880FF01D3B25 FOREIGN KEY (usuarios_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE detalles_orden ADD CONSTRAINT FK_FD5930413BA949C FOREIGN KEY (ordenes_id) REFERENCES ordenes (id)');
        $this->addSql('ALTER TABLE detalles_orden ADD CONSTRAINT FK_FD59304ED07566B FOREIGN KEY (productos_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE direcciones ADD CONSTRAINT FK_B0B0BECBF01D3B25 FOREIGN KEY (usuarios_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE ordenes ADD CONSTRAINT FK_55FDC04AF01D3B25 FOREIGN KEY (usuarios_id) REFERENCES usuarios (id)');
        $this->addSql('ALTER TABLE ordenes ADD CONSTRAINT FK_55FDC04AAAAC586 FOREIGN KEY (pagos_id) REFERENCES pagos (id)');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_767490E65792B277 FOREIGN KEY (categorias_id) REFERENCES categorias (id)');
        $this->addSql('ALTER TABLE productos_carrito ADD CONSTRAINT FK_4FF85FCA9DF172B5 FOREIGN KEY (carrito_compras_id) REFERENCES carrito_compras (id)');
        $this->addSql('ALTER TABLE productos_carrito ADD CONSTRAINT FK_4FF85FCAED07566B FOREIGN KEY (productos_id) REFERENCES productos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carrito_compras DROP FOREIGN KEY FK_F782880FF01D3B25');
        $this->addSql('ALTER TABLE detalles_orden DROP FOREIGN KEY FK_FD5930413BA949C');
        $this->addSql('ALTER TABLE detalles_orden DROP FOREIGN KEY FK_FD59304ED07566B');
        $this->addSql('ALTER TABLE direcciones DROP FOREIGN KEY FK_B0B0BECBF01D3B25');
        $this->addSql('ALTER TABLE ordenes DROP FOREIGN KEY FK_55FDC04AF01D3B25');
        $this->addSql('ALTER TABLE ordenes DROP FOREIGN KEY FK_55FDC04AAAAC586');
        $this->addSql('ALTER TABLE productos DROP FOREIGN KEY FK_767490E65792B277');
        $this->addSql('ALTER TABLE productos_carrito DROP FOREIGN KEY FK_4FF85FCA9DF172B5');
        $this->addSql('ALTER TABLE productos_carrito DROP FOREIGN KEY FK_4FF85FCAED07566B');
        $this->addSql('DROP TABLE carrito_compras');
        $this->addSql('DROP TABLE categorias');
        $this->addSql('DROP TABLE detalles_orden');
        $this->addSql('DROP TABLE direcciones');
        $this->addSql('DROP TABLE ordenes');
        $this->addSql('DROP TABLE pagos');
        $this->addSql('DROP TABLE productos');
        $this->addSql('DROP TABLE productos_carrito');
        $this->addSql('DROP TABLE usuarios');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
