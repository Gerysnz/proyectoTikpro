#!/usr/bin/env php
<?php
if (php_sapi_name() !== 'cli') {
    die("ERROR: Solo ejecucion desde terminal\n");
}

echo "========================================\n";
echo "      SCRIPT DE CARGA DE DATOS\n";
echo "========================================\n\n";

echo "¿Quieres continuar? (si/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

$respuesta = trim(strtolower($line));
if ($respuesta !== 'si' && $respuesta !== 's') {
    echo "\nOperacion cancelada.\n";
    exit(0);
}

echo "\nContinuando...\n\n";

require_once 'cats.php';

$dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
$db_user = 'root';
$db_pass = 'Heector7';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado a la base de datos...\n";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $tables = ['project_category', 'project', 'users', 'categories'];
    
    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE $table");
    }
    
    echo "Datos anteriores eliminados.\n";
    
    echo "\nInsertando categorias...\n";
    
    $stmt_cat = $pdo->prepare("INSERT INTO categories (Name, Parent_ID) VALUES (?, ?)");
    $inserted = 0;
    
    foreach ($categories as $category) {
        try {
            $name = $category['name'];
            $parent_id = $category['parent_id'];
            
            if (strlen($name) > 200) {
                $name = substr($name, 0, 197) . '...';
            }
            
            $stmt_cat->execute([$name, $parent_id]);
            $inserted++;
            
        } catch (Exception $e) {
        }
    }
    
    echo "Categorias insertadas: $inserted\n";
    
    echo "\nCreando 20 centros educativos...\n";
    
    $centros = [
        ['IES Esteve Terradas i Illa', 'info@iesterradas.cat', 'Institut public amb oferta de cicles formatius.', '934567890'],
        ['Institut La Ferreria', 'secretaria@ferreria.cat', 'Centre de referencia en formacio professional.', '934567891'],
        ['Institut Poblenou', 'contacte@inspoblenou.cat', 'Especialitzat en cicles de informatica i disseny.', '934567892'],
        ['IES Joan Brossa', 'administracio@iesjoanbrossa.cat', 'Centre amb tradicio en arts grafiques.', '934567893'],
        ['Institut L\'Hospitalet', 'info@ieshl.cat', 'Formacio professional en sanitat i serveis.', '934567894'],
        ['IES La Garrotxa', 'garrotxa@iesgarrotxa.cat', 'Referent en cicles agraris i medi ambient.', '934567895'],
        ['Institut Torre Vicens', 'torrevicens@iestorrevicens.cat', 'Especialitzat en electricitat i automocio.', '934567896'],
        ['IES Montserrat', 'montserrat@iesmontserrat.cat', 'Formacio en hosteleria i turisme.', '934567897'],
        ['Institut Provencana', 'provencana@iesprovencana.cat', 'Centre tecnologic amb cicles d\'informatica.', '934567898'],
        ['IES Salvador Dali', 'dali@iessalvadordali.cat', 'Especialitzat en imatge personal.', '934567899'],
        ['Institut Bonanova', 'bonanova@iesbonanova.cat', 'Centre historic amb cicles de grau superior.', '934567800'],
        ['IES Consell de Cent', 'consellcent@iesconsellcent.cat', 'Referent en comerc i marketing.', '934567801'],
        ['Institut Valles', 'valles@iesvalles.cat', 'Tecnologic amb cicles industrials.', '934567802'],
        ['IES La Guineueta', 'guineueta@iesguineueta.cat', 'Centre integral de formacio professional.', '934567803'],
        ['Institut Barri Besos', 'besos@iesbarribesos.cat', 'Especialitzat en construccio.', '934567804'],
        ['IES La Mina', 'lamina@ieslamina.cat', 'Centre de referencia en serveis socioculturals.', '934567805'],
        ['Institut Can Peixauet', 'canpeixauet@iescanpeixauet.cat', 'Formacio en quimica i industries alimentaries.', '934567806'],
        ['IES Icaria', 'icaria@iesicaria.cat', 'Centre tecnologic al districte 22@.', '934567807'],
        ['Institut Montjuic', 'montjuic@iesmontjuic.cat', 'Formacio professional amb vistes al mar.', '934567808'],
        ['IES Les Marines', 'marines@ieslesmarines.cat', 'Centre especialitzat en maritim-pesquera.', '934567809']
    ];
    
    $centers_ids = [];
    $stmt_user = $pdo->prepare("INSERT INTO users (user_name, user_surname, description, email, password, phone_number, user_type, entity_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($centros as $index => $centro) {
        $nombre_corto = explode(' ', $centro[0])[0];
        $password_hash = hash('sha256', 'centre' . ($index + 1));
        
        
        $stmt_user->execute([
            $nombre_corto,
            'Centre',
            $centro[2],
            $centro[1],
            $password_hash,
            $centro[3],
            'center',
            $centro[0],
        ]);
        
        $centers_ids[] = $pdo->lastInsertId();
    }
    
    echo "20 centros creados.\n";
    
    echo "\nCreando 20 empresas...\n";
    
    $empresas = [
        ['TechSolutions Barcelona', 'rrhh@techsolutions.cat', 'Empresa tecnologica especialitzada en desenvolupament de software.', '936543210'],
        ['Grafiques Modernes SL', 'info@grafiquesmodernes.cat', 'Impressio digital i arts grafiques desde 1995.', '936543211'],
        ['Restaurant Els Angels', 'angles@restaurant.cat', 'Restaurant gourmet amb estrella Michelin.', '936543212'],
        ['Clinica Dental Smile', 'smile@clinicadental.cat', 'Clinica dental especialitzada en ortodoncia.', '936543213'],
        ['AutoMecanica Rapid', 'taller@automecanica.cat', 'Taller mecanic amb 20 anys d\'experiencia.', '936543214'],
        ['Disseny Web Creatiu', 'hola@dissenyweb.cat', 'Agencia de disseny web i marketing digital.', '936543215'],
        ['Farmaceutics Associats', 'associats@farmacia.cat', 'Xarxa de farmacies amb servei integral.', '936543216'],
        ['Constructora Mediterrania', 'info@constructora.cat', 'Empresa de construccio i rehabilitacio.', '936543217'],
        ['Perruqueria Style', 'style@perruqueria.cat', 'Perruqueria i centre d\'estetica unisex.', '936543218'],
        ['Electricitat BCN', 'electric@electricitat.cat', 'Instal·lacions electriques i domotica.', '936543219'],
        ['Hotel Marina Beach', 'reserves@hotelmarina.cat', 'Hotel 4 estrelles a primera linia de platja.', '936543220'],
        ['Laboratoris Sanit', 'lab@sanit.cat', 'Laboratori d\'analisis cliniques i biomedicales.', '936543221'],
        ['Mecanitzats Precisio', 'precisio@mecanitzats.cat', 'Empresa de mecanitzacio CNC i prototipat.', '936543222'],
        ['Agricultura Ecologica', 'eco@agricultura.cat', 'Produccio i venda de productes ecologics.', '936543223'],
        ['Transportes Rapid', 'transport@rapid.cat', 'Empresa de transport i logistica.', '936543224'],
        ['Estetica Bella', 'bella@estetica.cat', 'Centre d\'estetica i benestar integral.', '936543225'],
        ['Informatica Pro', 'soporte@informaticapro.cat', 'Serveis informatics per a empreses.', '936543226'],
        ['Cuines Innovadores', 'cuines@innovadores.cat', 'Disseny i instal·lacio de cuines a mida.', '936543227'],
        ['Nautica Costa Brava', 'nautica@costabrava.cat', 'Manteniment i reparacio d\'embarcacions.', '936543228'],
        ['Moda Jove', 'moda@jove.cat', 'Disseny i confeccio de moda juvenil.', '936543229']
    ];
    
    $companies_ids = [];
    
    foreach ($empresas as $index => $empresa) {
        $nombre_corto = explode(' ', $empresa[0])[0];
        $password_hash = hash('sha256', 'empresa' . ($index + 1));
    
        
        $stmt_user->execute([
            $nombre_corto,
            'Empresa',
            $empresa[2],
            $empresa[1],
            $password_hash,
            $empresa[3],
            'company',
            $empresa[0]
        ]);
        
        $companies_ids[] = $pdo->lastInsertId();
    }
    
    echo "20 empresas creadas.\n";
    
    echo "\nCreando 6 proyectos...\n";
    
    $proyectos = [
        ['Projecte de Robotica Industrial', 'Els alumnes de Mecatronica presenten el seu projecte final.', 'video1.mp4'],
        ['Desenvolupament d\'App Mobil', 'Projecte final de DAM.', 'video2.mp4'],
        ['Analisi Quimica d\'Aigues', 'Practica de laboratori.', 'video3.mp4'],
        ['Taller de Cuina Mediterrania', 'Els alumnes preparen menu.', 'video4.mp4'],
        ['Disseny Grafic Editorial', 'Projecte de disseny.', 'video5.mp4'],
        ['Manteniment d\'Automobils', 'Practica de taller.', 'video6.mp4']
    ];
    
    $project_ids = [];
    $stmt_project = $pdo->prepare("INSERT INTO project (User_id, title, description, image_path, video_path) VALUES (?, ?, ?, ?, ?)");
    
    for ($i = 0; $i < 6; $i++) {
        $center_id = $centers_ids[$i];
        $proyecto = $proyectos[$i];

        $logo_num = ($i% 6) + 1;
        $logo_file = "logo$logo_num.jpeg";
        
        $stmt_project->execute([
            $center_id,
            $proyecto[0],
            $proyecto[1],
            $logo_file,
            $proyecto[2]
        ]);
        
        $project_ids[] = $pdo->lastInsertId();
        echo "Proyecto: " . $proyecto[0] . "\n";
    }
    
    echo "6 proyectos creados.\n";
    
    echo "\nCreando carpeta uploads/...\n";
    
    $uploads_dir = __DIR__ . '/../uploads/';
    if (!file_exists($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
        echo "Carpeta uploads/ creada.\n";
    } else {
        echo "Carpeta uploads/ ya existe.\n";
    }
    
    echo "\nCopiando videos a uploads/...\n";
    
    for ($i = 1; $i <= 6; $i++) {
        $video_file = "video$i.mp4";
        $source = __DIR__ . "/$video_file";
        $destination = $uploads_dir . $video_file;
        
        if (file_exists($source)) {
            if (copy($source, $destination)) {
                echo "Video copiado: $video_file\n";
            } else {
                echo "Error copiando: $video_file\n";
            }
        } else {
            echo "Video no encontrado: $video_file (creando dummy)\n";
            file_put_contents($destination, "Dummy video $i");
        }
    }
    
    echo "\nCopiando logos a uploads/...\n";
    
    for ($i = 1; $i <= 6; $i++) {
        $logo_file = "logo$i.jpeg";
        $source = __DIR__ . "/$logo_file";
        $destination = $uploads_dir . $logo_file;
        
        if (file_exists($source)) {
            if (copy($source, $destination)) {
                echo "Logo copiado: $logo_file\n";
            } else {
                echo "Error copiando: $logo_file\n";
            }
        } else {
            echo "Logo no encontrado: $logo_file (creando dummy)\n";
            file_put_contents($destination, "Dummy logo $i");
        }
    }
    
    echo "\nAsignando categorias...\n";
    
    $stmt_get_cats = $pdo->query("SELECT Category_ID, Name FROM categories WHERE Parent_ID != 0 AND Name LIKE '%-%' LIMIT 20");
    $categorias_ciclos = $stmt_get_cats->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($categorias_ciclos) > 0) {
        $stmt_proj_cat = $pdo->prepare("INSERT INTO project_category (Project_ID, Category_ID) VALUES (?, ?)");
        $total_asignaciones = 0;
        
        foreach ($project_ids as $project_id) {
            $num_cats = rand(2, 4);
            $available_cats = array_keys($categorias_ciclos);
            shuffle($available_cats);
            $selected_cats = array_slice($available_cats, 0, min($num_cats, count($available_cats)));
            
            foreach ($selected_cats as $cat_index) {
                $cat_id = $categorias_ciclos[$cat_index]['Category_ID'];
                $stmt_proj_cat->execute([$project_id, $cat_id]);
                $total_asignaciones++;
            }
        }
        
        echo "Asignaciones: $total_asignaciones\n";
    } else {
        echo "No categorias para asignar\n";
        $total_asignaciones = 0;
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SEEDER COMPLETADO\n";
    echo str_repeat("=", 50) . "\n";
    echo "Categorias: $inserted\n";
    echo "Usuarios: " . (count($centers_ids) + count($companies_ids)) . "\n";
    echo "  Centros: " . count($centers_ids) . "\n";
    echo "  Empresas: " . count($companies_ids) . "\n";
    echo "Proyectos: " . count($project_ids) . "\n";
    echo "Asignaciones: $total_asignaciones\n";
    echo "Archivos en uploads/: videos + logos\n";
    echo str_repeat("=", 50) . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    if (isset($pdo)) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
}
?>