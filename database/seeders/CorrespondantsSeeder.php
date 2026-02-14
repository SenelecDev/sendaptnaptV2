<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrespondantsSeeder extends Seeder
{
    public function run(): void
    {
        $correspondants = [
            ['nom' => 'Abdoulaye Touré', 'fonction' => 'Chef de service OMVG', 'matricule' => 'C0897', 'telephone' => '775697910'],
            ['nom' => 'Ibrahima Diarra Diouf', 'fonction' => 'Chef de service SEP', 'matricule' => 'C01269', 'telephone' => '773326972'],
            ['nom' => 'Moussa Mbodj', 'fonction' => 'Chef de Poste', 'matricule' => 'M07877', 'telephone' => '77416844'],
            ['nom' => 'Ibrahima Dieng', 'fonction' => 'Chef de Poste', 'matricule' => 'M07464', 'telephone' => '781490190'],
            ['nom' => 'Papa Amadou Fall', 'fonction' => 'Chef de Poste', 'matricule' => 'M07966', 'telephone' => '773687663'],
            ['nom' => 'Elhadji Omar Sene', 'fonction' => 'Chef de Poste', 'matricule' => 'M06090', 'telephone' => '786395868'],
            ['nom' => 'Abdou Haad Ndiaye', 'fonction' => 'Chef de Poste', 'matricule' => 'M04991', 'telephone' => '778195533'],
            ['nom' => 'Doudou Sow', 'fonction' => 'Chef de Poste', 'matricule' => 'M05354', 'telephone' => '778195454'],
            ['nom' => 'Flavien Aimé Mansal', 'fonction' => 'Chef de Poste', 'matricule' => 'M05150', 'telephone' => '777408201'],
            ['nom' => 'Assane Diakhaté', 'fonction' => 'Chef de Poste', 'matricule' => 'M05111', 'telephone' => '777408200'],
            ['nom' => 'Alioune Ndoye', 'fonction' => 'Chef de Poste', 'matricule' => 'M07913', 'telephone' => '773324884'],
            ['nom' => 'Mamadou Aliou Diallo', 'fonction' => 'Chef de Poste', 'matricule' => 'M05120', 'telephone' => '786370834'],
            ['nom' => 'Abibou Diop', 'fonction' => 'Chef de Poste', 'matricule' => 'M04874', 'telephone' => '774505027'],
            ['nom' => 'Ousseynou Diop', 'fonction' => 'Chef de Poste', 'matricule' => 'M06767', 'telephone' => '785897063'],
            ['nom' => 'Mohameth Gadiaga', 'fonction' => 'Chef de Poste', 'matricule' => 'M05829', 'telephone' => '786395878'],
            ['nom' => 'Diégane Sene', 'fonction' => 'Chef de Poste', 'matricule' => 'M07021', 'telephone' => '786371843'],
            ['nom' => 'Cheikhou Gueye', 'fonction' => 'Chef de Poste', 'matricule' => 'M06626', 'telephone' => '777408244'],
            ['nom' => 'Cheikh Ahmed Tidiane Camara', 'fonction' => 'Chef de Poste', 'matricule' => 'M07883', 'telephone' => '774616898'],
            ['nom' => 'Elh Malick Mbengue', 'fonction' => 'Chef de Poste', 'matricule' => 'M07885', 'telephone' => '774616458'],
            ['nom' => 'Yougo Gning', 'fonction' => 'Chef de Poste', 'matricule' => 'M06855', 'telephone' => '771993969'],
            ['nom' => 'Cheikh Seydi Malick Diop', 'fonction' => 'Chef de Poste', 'matricule' => 'M05890', 'telephone' => '778195539'],
            ['nom' => 'Modou Diouf', 'fonction' => 'Chef de Poste', 'matricule' => 'M06114', 'telephone' => '773587219'],
            ['nom' => 'Moulaye Fall', 'fonction' => 'Chef de Poste', 'matricule' => 'M08279', 'telephone' => '777408235'],
            ['nom' => 'Moussa Demba Thiam', 'fonction' => 'Chef de Poste', 'matricule' => 'M08282', 'telephone' => '773586974'],
            ['nom' => 'Thierno Dieye', 'fonction' => 'Chef de Poste', 'matricule' => 'M08277', 'telephone' => '773590865'],
            ['nom' => 'Ali Ba', 'fonction' => 'Chef de Poste', 'matricule' => 'M08284', 'telephone' => '773324720'],
            ['nom' => 'Mohamed Sarr', 'fonction' => 'Chef de Poste', 'matricule' => 'M08283', 'telephone' => '773587058'],
            ['nom' => 'Ababacar Faye', 'fonction' => 'Chef de Poste', 'matricule' => 'M08280', 'telephone' => '773587096'],
            ['nom' => 'Serigne C A Khadre Cassé', 'fonction' => 'Chef de Poste', 'matricule' => 'M08276', 'telephone' => '773587238'],
            ['nom' => 'Cheikhou Lakhamy Samoussa', 'fonction' => 'Chef de Poste', 'matricule' => 'M08278', 'telephone' => '773587069'],
            ['nom' => 'Samba Faye', 'fonction' => 'Chef de Poste', 'matricule' => 'M08342', 'telephone' => '773587128'],
            ['nom' => 'Mor Gueye', 'fonction' => 'Chef de Poste', 'matricule' => 'M08361', 'telephone' => '775691986'],
            ['nom' => 'Mansour Diop', 'fonction' => 'Chef de Poste', 'matricule' => 'M08281', 'telephone' => '773587232'],
            ['nom' => 'Papa Gallo Fall Diallo', 'fonction' => 'Chef de Poste', 'matricule' => 'M07353', 'telephone' => '773688256'],
            ['nom' => 'Adama Cissé', 'fonction' => 'Chef de Poste', 'matricule' => 'M07354', 'telephone' => '773688147'],
            ['nom' => 'Djibril Diagne Gueye', 'fonction' => 'Chef de Poste', 'matricule' => 'M07355', 'telephone' => '773688141'],
            ['nom' => 'Papa Mamadou Gueye', 'fonction' => 'Chef de Poste', 'matricule' => 'M07357', 'telephone' => '773688074'],
            ['nom' => 'El Hadj Momar GADJ', 'fonction' => 'Chef de poste', 'matricule' => 'M00000', 'telephone' => '445556677'],
            ['nom' => 'Cheikh Ahmadou Bamba LO', 'fonction' => 'Chef de poste', 'matricule' => 'M08672', 'telephone' => '223334455'],
            ['nom' => 'Mader DIOP', 'fonction' => 'Agent Polyvalent', 'matricule' => 'M07202', 'telephone' => '783010238'],
            ['nom' => 'Babacar DIALLO', 'fonction' => 'Chef de Poste', 'matricule' => 'M08096', 'telephone' => '334445566'],
            ['nom' => 'Yacine Gueye LO', 'fonction' => 'Chef de Poste', 'matricule' => 'M08632', 'telephone' => '778889900'],
            ['nom' => 'Oumar SECK', 'fonction' => 'Chef de Poste', 'matricule' => 'M08620', 'telephone' => '771112233'],
            ['nom' => 'Waly DIACKO', 'fonction' => 'Chauffeur', 'matricule' => 'M08482', 'telephone' => '789990088'],
            ['nom' => 'Iboun Oumar MBALLO', 'fonction' => 'Chef de poste', 'matricule' => 'M08087', 'telephone' => '112223344'],
        ];

        $now = now();
        
        foreach ($correspondants as $correspondant) {
            // Vérifier si le matricule existe déjà
            $exists = DB::table('correspondants')->where('matricule', $correspondant['matricule'])->exists();
            
            if (!$exists) {
                DB::table('correspondants')->insert([
                    'nom' => $correspondant['nom'],
                    'fonction' => $correspondant['fonction'],
                    'matricule' => $correspondant['matricule'],
                    'telephone' => $correspondant['telephone'],
                    'adresse' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('✅ ' . count($correspondants) . ' correspondants insérés.');

        // Insérer aussi dans charges_cons
        $insertedCharges = 0;
        foreach ($correspondants as $correspondant) {
            // Vérifier si le matricule existe déjà dans charges_cons
            $exists = DB::table('charges_cons')->where('matricule', $correspondant['matricule'])->exists();
            
            if (!$exists) {
                DB::table('charges_cons')->insert([
                    'nom' => $correspondant['nom'],
                    'fonction' => $correspondant['fonction'],
                    'matricule' => $correspondant['matricule'],
                    'telephone' => $correspondant['telephone'],
                    'adresse' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $insertedCharges++;
            }
        }

        $this->command->info('✅ ' . $insertedCharges . ' chargés de consignation insérés.');
    }
}
