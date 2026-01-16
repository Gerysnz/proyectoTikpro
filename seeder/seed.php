<?php
require_once 'cats.php';

$dsn = 'mysql:host=localhost;dbname=project_platform;charset=utf8';
$db_user = 'gery';
$db_pass = 'superlocal';

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado a la base de datos project_platform...\n";
    echo "\nLimpiando datos existentes...\n";
    
    echo "\nInsertando categorías...\n";
    
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
            echo "  Error: " . $e->getMessage() . " - Nombre: $name\n";
        }
    }
    
    echo "Categorías insertadas: $inserted\n";
    
    echo "\nCreando 20 centros educativos...\n";
    
    $centros = [
        ['IES Esteve Terradas i Illa', 'info@iesterradas.cat', 'Institut públic amb una àmplia oferta de cicles formatius.', '934567890'],
        ['Institut La Ferreria', 'secretaria@ferreria.cat', 'Centre de referència en formació professional.', '934567891'],
        ['Institut Poblenou', 'contacte@inspoblenou.cat', 'Especialitzat en cicles de informàtica i disseny.', '934567892'],
        ['IES Joan Brossa', 'administracio@iesjoanbrossa.cat', 'Centre amb tradició en arts gràfiques.', '934567893'],
        ['Institut L\'Hospitalet', 'info@ieshl.cat', 'Formació professional en sanitat i serveis.', '934567894'],
        ['IES La Garrotxa', 'garrotxa@iesgarrotxa.cat', 'Referent en cicles agraris i medi ambient.', '934567895'],
        ['Institut Torre Vicens', 'torrevicens@iestorrevicens.cat', 'Especialitzat en electricitat i automoció.', '934567896'],
        ['IES Montserrat', 'montserrat@iesmontserrat.cat', 'Formació en hosteleria i turisme.', '934567897'],
        ['Institut Provençana', 'provencana@iesprovencana.cat', 'Centre tecnològic amb cicles d\'informàtica.', '934567898'],
        ['IES Salvador Dalí', 'dali@iessalvadordali.cat', 'Especialitzat en imatge personal.', '934567899'],
        ['Institut Bonanova', 'bonanova@iesbonanova.cat', 'Centre històric amb cicles de grau superior.', '934567800'],
        ['IES Consell de Cent', 'consellcent@iesconsellcent.cat', 'Referent en comerç i màrqueting.', '934567801'],
        ['Institut Vallès', 'valles@iesvalles.cat', 'Tecnològic amb cicles industrials.', '934567802'],
        ['IES La Guineueta', 'guineueta@iesguineueta.cat', 'Centre integral de formació professional.', '934567803'],
        ['Institut Barri Besòs', 'besos@iesbarribesos.cat', 'Especialitzat en construcció.', '934567804'],
        ['IES La Mina', 'lamina@ieslamina.cat', 'Centre de referència en serveis socioculturals.', '934567805'],
        ['Institut Can Peixauet', 'canpeixauet@iescanpeixauet.cat', 'Formació en química i indústries alimentàries.', '934567806'],
        ['IES Icària', 'icaria@iesicaria.cat', 'Centre tecnològic al districte 22@.', '934567807'],
        ['Institut Montjuïc', 'montjuic@iesmontjuic.cat', 'Formació professional amb vistes al mar.', '934567808'],
        ['IES Les Marines', 'marines@ieslesmarines.cat', 'Centre especialitzat en marítim-pesquera.', '934567809']
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
            $centro[0]
        ]);
        
        $centers_ids[] = $pdo->lastInsertId();
    }
    
    echo "20 centros educativos creados\n";
    
    echo "\nCreando 20 empresas...\n";
    
    $empresas = [
        ['TechSolutions Barcelona', 'rrhh@techsolutions.cat', 'Empresa tecnològica especialitzada en desenvolupament de software.', '936543210'],
        ['Gràfiques Modernes SL', 'info@grafiquesmodernes.cat', 'Impressió digital i arts gràfiques des de 1995.', '936543211'],
        ['Restaurant Els Àngels', 'angles@restaurant.cat', 'Restaurant gourmet amb estrella Michelin.', '936543212'],
        ['Clínica Dental Smile', 'smile@clinicadental.cat', 'Clínica dental especialitzada en ortodoncia.', '936543213'],
        ['AutoMecànica Ràpid', 'taller@automecanica.cat', 'Taller mecànic amb 20 anys d\'experiència.', '936543214'],
        ['Disseny Web Creatiu', 'hola@dissenyweb.cat', 'Agència de disseny web i màrqueting digital.', '936543215'],
        ['Farmacèutics Associats', 'associats@farmacia.cat', 'Xarxa de farmàcies amb servei integral.', '936543216'],
        ['Constructora Mediterrània', 'info@constructora.cat', 'Empresa de construcció i rehabilitació.', '936543217'],
        ['Perruqueria Style', 'style@perruqueria.cat', 'Perruqueria i centre d\'estètica unisex.', '936543218'],
        ['Electricitat BCN', 'electric@electricitat.cat', 'Instal·lacions elèctriques i domòtica.', '936543219'],
        ['Hotel Marina Beach', 'reserves@hotelmarina.cat', 'Hotel 4 estrelles a primera línia de platja.', '936543220'],
        ['Laboratoris Sanit', 'lab@sanit.cat', 'Laboratori d\'anàlisis clíniques i biomèdiques.', '936543221'],
        ['Mecanitzats Precisió', 'precisio@mecanitzats.cat', 'Empresa de mecanització CNC i prototipat.', '936543222'],
        ['Agricultura Ecològica', 'eco@agricultura.cat', 'Producció i venda de productes ecològics.', '936543223'],
        ['Transportes Ràpid', 'transport@rapid.cat', 'Empresa de transport i logística.', '936543224'],
        ['Estètica Bella', 'bella@estetica.cat', 'Centre d\'estètica i benestar integral.', '936543225'],
        ['Informàtica Pro', 'soporte@informaticapro.cat', 'Serveis informàtics per a empreses.', '936543226'],
        ['Cuines Innovadores', 'cuines@innovadores.cat', 'Disseny i instal·lació de cuines a mida.', '936543227'],
        ['Nàutica Costa Brava', 'nautica@costabrava.cat', 'Manteniment i reparació d\'embarcacions.', '936543228'],
        ['Moda Jove', 'moda@jove.cat', 'Disseny i confecció de moda juvenil.', '936543229']
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
    
    echo "\nCreando 6 proyectos con vídeos...\n";

    // Función para generar label desde title
    function generateLabel($text) {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/\s+/', '-', trim($text));
        return $text;
    }
    
    $proyectos = [
        ['Projecte de Robòtica Industrial', 'Els alumnes de Mecatrònica presenten el seu projecte final de robòtica industrial.', '/uploads/video1.mp4'],
        ['Desenvolupament d\'App Mòbil', 'Projecte final de DAM: una aplicació mòbil per a la gestió d\'esdeveniments.', '/uploads/video2.mp4'],
        ['Anàlisi Química d\'Aigües', 'Pràctica de laboratori on s\'analitzen mostres d\'aigua de diferents fonts.', '/uploads/video3.mp4'],
        ['Taller de Cuina Mediterrània', 'Els alumnes de Cuina preparen un menú complet de cuina mediterrània.', '/uploads/video4.mp4'],
        ['Disseny Gràfic Editorial', 'Projecte de disseny d\'una revista digital amb contingut cultural.', '/uploads/video5.mp4'],
        ['Manteniment d\'Automòbils', 'Pràctica de taller on es realitza el manteniment complet d\'un vehicle.', '/uploads/video6.mp4']
    ];
    
    $project_ids = [];
    $stmt_project = $pdo->prepare(
        "INSERT INTO project (User_id, title, description, image_path, video_path, label)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    for ($i = 0; $i < 6; $i++) {
        $center_id = $centers_ids[$i];
        $proyecto = $proyectos[$i];

        $logo_num = ($i % 6) + 1;
        $logo_file = "logo$logo_num.jpeg";

        $label = generateLabel($proyecto[0]);
        
        $stmt_project->execute([
            $center_id,
            $proyecto[0],
            $proyecto[1],
            $logo_file,
            $proyecto[2],
            $label
        ]);
        
        $project_ids[] = $pdo->lastInsertId();
        echo "Proyecto creado: " . $proyecto[0] . " (label: $label, vídeo: " . $proyecto[2] . ")\n";
    }
    
    echo "6 proyectos creados.\n";

    echo "\nAsignando categorías a proyectos...\n";
    
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
        
        echo "$total_asignaciones asignaciones proyecto-categoría creadas.\n";
    } else {
        echo "No se encontraron categorías para asignar\n";
        $total_asignaciones = 0;
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SEEDER COMPLETADO EXITOSAMENTE!\n";
    echo str_repeat("=", 50) . "\n";
    echo "RESUMEN:\n";
    echo "Categorías insertadas: $inserted\n";
    echo "Usuarios totales: " . (count($centers_ids) + count($companies_ids)) . "\n";
    echo "  Centros (user_type='center'): " . count($centers_ids) . "\n";
    echo "  Empresas (user_type='company'): " . count($companies_ids) . "\n";
    echo "Proyectos: " . count($project_ids) . "\n";
    echo "Asignaciones proyecto-categoría: $total_asignaciones\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "\n=== INFORMACIÓN PARA LOGIN ===\n";
    echo "Centros (usar password 'centre1', 'centre2', etc):\n";
    $stmt = $pdo->query("SELECT email FROM users WHERE user_type = 'center' LIMIT 3");
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($emails as $i => $email) {
        echo "  Email: $email - Password: centre" . ($i + 1) . "\n";
    }
    
    echo "\nEmpresas (usar password 'empresa1', 'empresa2', etc):\n";
    $stmt = $pdo->query("SELECT email FROM users WHERE user_type = 'company' LIMIT 3");
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($emails as $i => $email) {
        echo "  Email: $email - Password: empresa" . ($i + 1) . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error en el seeder: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    
    if (isset($pdo)) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
}
?>
