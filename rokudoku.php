<?php

class Rokudoku{

    protected $ime_igraca, $broj_pokusaja, $gotovo;
	protected $tablica_rokudoku = array(), $tablica_pocetna = array();
	protected $errorMsg;

	function __construct(){
		$this->ime_igraca = false;
		$this->broj_pokusaja = 0;
		$this->gotovo = false;
		$this->errorMsg = false;
    }

    function inicijaliziraj( $tablica ){

		//definiram rokudoku kao tablicu brojeva 
		//brojeve je odabrao igrač pri izboru easy, medium, hard rokudokua
		for( $r = 0; $r < 6 ; $r++ ){
			for( $s = 0; $s < 6; $s++ ){				
				($this->tablica_rokudoku)[$r][$s] = $tablica[$r][$s];
				($this->tablica_pocetna)[$r][$s] = $tablica[$r][$s];
			}
        }
        
		$this->ime_igraca = false;
		$this->broj_pokusaja = 0;
		$this->gotovo = false;
		$this->errorMsg = false;
    }

    //ispis početne forme u koju igrač nakon unosa svog imena i odabira Rokudoku kojeg želi riješiti započinje igru
    //ukoliko je unos imena neispravan ispisuje se poruka o grešci    
    function ispisi_pocetnu_formu(){
	?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Rokudoku</title>
            <style>
            <?php include 'style.css'; ?>
			</style>
        </head>
        <body>
            <h1>Rokudoku!</h1>
            <form action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>" method="post">
				Unesi svoje ime: 
                <input type="text" name="ime_igraca">
                <br /><br />
                <span class="greska">
                <?php if( $this->errorMsg !== false ) echo htmlentities( $this->errorMsg ) . '</p>'; ?>
                </span>
                <br />
                
                Odaberite rokudoku željene težine.
				<select name="rokudoku">
					<option value="easy" selected>Rokudoku easy</option>
					<option value="medium">Rokudoku medium</option>
					<option value="hard">Rokudoku hard</option>
				</select>
				<br><br><br>
				<button type="submit">Započni igru!</button>
			</form>


        </body>
        </html>

    <?php
    }

    function get_ime_igraca(){

		//je li već ime definirano
		if( $this->ime_igraca !== false )
			return $this->ime_igraca;

		//je li se ime upravo šalje
		if( isset( $_POST['ime_igraca'] ) ){
			//provjeri ispravnost imena (samo od slova i to najviše 20)
			if( !preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['ime_igraca'] ) ){
				//ime nije dobro 
				$this->errorMsg = 'Neispravan unos imena! Ime igrača treba imati između 1 i 20 slova.';
				return false;
			}
			else{
				//ime je dobro
				$this->ime_igraca = $_POST['ime_igraca'];
				return $this->ime_igraca;
			}
		}
		//niti imamo ime niti se trenutno šalje
		return false;
	}

    function izgled_broja($r, $s){

        if($this->tablica_pocetna[$r][$s] !== '')
            return 'podebljan';
        elseif ($this->tablica_rokudoku[$r][$s] == '')
            return 'obican';
        elseif ( $this->je_li_dozvoljen($this->tablica_rokudoku[$r][$s], $r, $s) )
            return 'plav';
        elseif ( !$this->je_li_dozvoljen($this->tablica_rokudoku[$r][$s], $r, $s) )
            return 'crven';
    }

    //dani broj smije biti na poziciji ($pozicija_x, $pozicija_y) ako se taj isti broj već ne nalazi
    //u istom retku, istom stupcu ili istom 2x3 pravokutniku
    function je_li_dozvoljen( $broj, $pozicija_x, $pozicija_y){
        
        //provjera retka
        for($r = 0; $r < 6; ++$r){   
            if($r !== $pozicija_x){
                if($this->tablica_rokudoku[$r][$pozicija_y] === $broj){    
                    return false;
                }
            }
        }   
        //provjera stupca
        for($s = 0; $s < 6; ++$s){   
            if($s !== $pozicija_y){
                if($this->tablica_rokudoku[$pozicija_x][$s] === $broj){
                    return false;
                }
            }
        }
        //provjera pravokutnika
        $redak_gore = 0;
        $redak_dolje = 0;
        $pravokutnik_lijevo = 0;
        $pravokutnik_desno = 0;
            //redak pravokutnika
            if($pozicija_x%2 === 0)
                $drugi_redak = $pozicija_x+1;
            else $drugi_redak = $pozicija_x-1;
            //stupac pravokutnika
            if($pozicija_y < 3)
                $pocetna_pozicija = 0;
            else $pocetna_pozicija = 3;

            for($i=0; $i<3; ++$i){
                if ($pocetna_pozicija+$i !== $pozicija_y){ 
                    if ( $this->tablica_rokudoku[$pozicija_x][$pocetna_pozicija+$i] === $broj ){
                        return false;
                    }
                    if ( $this->tablica_rokudoku[$drugi_redak][$pocetna_pozicija+$i] === $broj ){
                        return false;
                    }
                }
            }
        return true;
    }


    function ispisi_rokudoku_formu(){
        ++$this->broj_pokusaja;
	?>
		<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Rokudoku</title>
            <style>
            <?php include 'style.css'; ?>
			</style>
        </head>
        <body>
            <h1>Rokudoku!</h1>
            <p>
				Igrač:  <?php echo htmlentities( $this->ime_igraca ); ?>
				<br />
				Broj pokušaja: <?php echo htmlentities( $this->broj_pokusaja ); ?>
                <br />
                <br />
                <span class="greska">
                <?php echo htmlentities( $this->errorMsg ); ?>
                </span>
                
			</p>
            
<?php
            echo '<table>';
            for( $red = 0; $red < 6; ++$red ){
                echo '<tr>';
                for( $stup = 0; $stup < 6; ++$stup){
                    echo '<td class="' . $this->izgled_broja($red, $stup) . '">';
                    echo '<h2>' . $this->tablica_rokudoku[$red][$stup] . '</h2>';
                    echo '<span class="moguci_brojevi">' . $this->moguci_brojevi($red, $stup) . '</span>';
                    echo '</td>';
                }
            }

        echo '</table>';
?>


            <form action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>" method="post">
				<br />
                <input type='radio' id='unos' name='odabir' value='unos'>
                <label for='unos'> Unesi broj <input id='unos_broja' name='unos_broja' type='text' /> u redak 
                    <select name='broj_retka'>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                        <option value='5'>5</option>
                        <option value='6'>6</option>
                    </select>
                    i stupac 
                    <select name='broj_stupca'>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                        <option value='5'>5</option>
                        <option value='6'>6</option>
                    </select></label>
                <br />
                <input type='radio' id='obrisi' name='odabir' value='obrisi'>
                <label for='obrisi'>Obriši broj iz 
                    <select name='obrisi_iz_retka'>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                        <option value='5'>5</option>
                        <option value='6'>6</option>
                    </select>
                    retka i 
                    <select name='obrisi_iz_stupca'>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                        <option value='5'>5</option>
                        <option value='6'>6</option>
                    </select>
                    stupca.
                </label>
                <br />
                <input type='radio' id='reset' name='odabir' value='reset'>
                <label for='reset'>Želim sve ispočetka!</label>
                <br />
                <br />
                <input type='submit' name='submit' value='Izvrši akciju!'> 
            </form>
<?php
}

    function obradi_akciju(){

        //igrac je odabrao unos
        if (isset($_POST['odabir'])){
            if ( $_POST['odabir'] == 'unos'){
                //ako je unesen krivi broj 
                if( !preg_match('/^-?[1-6]+$/', $_POST['unos_broja']) ){	
                    $this->errorMsg = 'Neispravan unos! Broj treba biti između 1 i 6.';
                    $this->ispisi_rokudoku_formu();
                    return;
                }
                //nije unesen krivi broj
                $broj_za_unos = $_POST['unos_broja'];
                $redak = $_POST['broj_retka']-1;
                $stupac = $_POST['broj_stupca']-1;

                 //smije se prebrisati broj upisani broj, ali ne i zadani
                if ($this->tablica_pocetna[$redak][$stupac] == '')
                    $this->tablica_rokudoku[$redak][$stupac] = $broj_za_unos;
                else
                    $this->errorMsg = 'Neispravan unos! Ne smijete staviti broj na poziciju zadanog elementa.';

                //provjera je li ovim unosom igra gotova
                $this->gotovo = true;
                for($r = 0; $r < 6; $r++){
                    for($s = 0; $s < 6; $s++){
                        //ako ima neko prazno ili ako ima neki broj na nedozvoljenom mjestu igra nije gotova
                        if($this->tablica_rokudoku[$r][$s] === '' || !$this->je_li_dozvoljen($this->tablica_rokudoku[$r][$s], $r, $s)){   
                            $this->gotovo = false;
                        }
                    }
                }
                if( $this->je_li_gotovo() ){
                    $this->ispisi_cestitku();
                }
                else{
                    $this->ispisi_rokudoku_formu();
                }
            }
 
            //igrac je odabrao brisanje
            else if ( $_POST['odabir'] == 'obrisi'){

                $redak_za_brisanje = $_POST['obrisi_iz_retka']-1;
                $stupac_za_brisanje = $_POST['obrisi_iz_stupca']-1;
                //igrac ne smije obrisati zadani element
                if ($this->tablica_pocetna[$redak_za_brisanje][$stupac_za_brisanje] == '')
                    $this->tablica_rokudoku[$redak_za_brisanje][$stupac_za_brisanje] = '';
                else
                    $this->errorMsg = "Neispravno brisanje! Ne smijete obrisati zadani element.";

                $this->ispisi_rokudoku_formu();
            }

            //igrac je odabrao sve ispočetka
            else if ($_POST['odabir'] == 'reset'){
                
                $this->broj_pokusaja = -1;
        
                for( $r = 0; $r < 6 ; $r++ ){
                    for( $s = 0; $s < 6; $s++ ){				
                        $this->tablica_rokudoku[$r][$s] = $this->tablica_pocetna[$r][$s];
                    }
                }
                $this->ispisi_rokudoku_formu();
            }

        }
        //inače nije odabrao nikakvu akciju
        else{
            --$this->broj_pokusaja;
            $this->ispisi_rokudoku_formu();
		}

    }

    function je_li_gotovo() { 
        return $this->gotovo;
    }

    function ispisi_cestitku(){
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>Rokudoku!</title>
			<style>
			<?php include 'style.css'; ?>
			</style>
		</head>
		<body>
			<h1>Rokudoku!</h1>
			<p>
				Bravo, <?php echo htmlentities( $this->ime_igraca ); ?>!
				<br />
				Uspješno ste riješili rokudoku u <?php echo $this->broj_pokusaja+1; ?> pokušaja!
			</p>
		</body>
		</html>

	<?php
	}

    function run(){
		//prvo resetiraj poruke o grešci
        $this->errorMsg = false;

        //imamo li igrača
        //ako nemamo ime igrača ispiši formu za unos imena
		if( $this->get_ime_igraca() === false ){
			$this->ispisi_pocetnu_formu();
			return;
        }
        //inače imamo igrača
        $this->obradi_akciju();
    }

//dodatna funkcija za ispis brojeva koji se mogu unijeti u kvadratić
    function moguci_brojevi( $pozicija_x, $pozicija_y){
        $brojevi = array();
        $string = '';

        //zadano je brojeve ispisivati samo u praznim kvadratićima
        if ($this->tablica_rokudoku[$pozicija_x][$pozicija_y]!==''){
            return $string;
        }
        else{
            
            for($r = 1; $r <= 6; ++$r){
                $brojevi[$r] = $r;
            }

            //provjera retka
            //kad naiđe na neprazni kvadratić u retku u kojem se nalazi onaj u koju upisujemo
            //prebriše nulom element niza $brojevi koji je jednak nađenom elementu
            for($r = 0; $r < 6; ++$r){   
                if($this->tablica_rokudoku[$r][$pozicija_y] !== ''){    
                    $brojevi[$this->tablica_rokudoku[$r][$pozicija_y]] = 0;
                }
            }           
            //provjera stupca
            for($s = 0; $s < 6; ++$s){   
                if($this->tablica_rokudoku[$pozicija_x][$s] !== ''){
                    $brojevi[$this->tablica_rokudoku[$pozicija_x][$s]] = 0;
                }
            }

            //provjera pravokutnika
            $redak_gore = 0;
            $redak_dolje = 0;
            $pravokutnik_lijevo = 0;
            $pravokutnik_desno = 0;
            //redak pravokutnika
            if($pozicija_x%2 === 0)
                $drugi_redak = $pozicija_x+1;
            else $drugi_redak = $pozicija_x-1;
            //stupac pravokutnika
            if($pozicija_y < 3)
                $pocetna_pozicija = 0;
            else $pocetna_pozicija = 3;
            
            for($i = 0; $i < 3; ++$i){
            
                if ( $this->tablica_rokudoku[$pozicija_x][$pocetna_pozicija+$i] !== '' ){
                    $brojevi[$this->tablica_rokudoku[$pozicija_x][$pocetna_pozicija+$i]] = 0;
                }
                if ( $this->tablica_rokudoku[$drugi_redak][$pocetna_pozicija+$i] !== '' ){
                    $brojevi[$this->tablica_rokudoku[$drugi_redak][$pocetna_pozicija+$i]] = 0;
                }
            }

            //dozvoljene brojeve pišemo u string koji ćemo ispisivati u praznim kvadratićima
            for($i = 1; $i <= 6; ++$i){
                if($brojevi[$i] !== 0)
                    $string = $string . $brojevi[$i];
            }
            
            return $string;
        }
    }
};

//u $_SESSION čuvamo cijeli objekt tipa Rokudoku
session_start();

//ako igra još nije započela, stvori novi objekt Rokudoku i spremi ga u $_SESSION
if( !isset( $_SESSION['igra'] ) ){
	$igra = new Rokudoku();  
    $_SESSION['igra'] = $igra;
}
//ako je igra već ranije započela, dohvati ju iz $_SESSION-a
else{

    $rokudoku_easy = array (
		array('1','','3', '','4',''),
		array('','6','', '2','','3'),
		array('','4','', '3','','1'),
		array('3','','2', '4','6',''),
		array('2','','1', '','5',''),
		array('','5','', '1','','2')
    );

    $rokudoku_medium = array (
		array('','','4', '','',''),
		array('','','', '2','3',''),
		array('3','','', '','6',''),
		array('','6','', '','','2'),
		array('','2','1', '','',''),
		array('','','', '5','','')
    );
    
    // $rokudoku_medium = array (
	// 	array('2','3','4', '1','5','6'),
	// 	array('1','5','6', '2','3','4'),
	// 	array('3','1','2', '4','6','5'),
	// 	array('4','6','5', '3','1','2'),
	// 	array('5','2','1', '6','4','3'),
	// 	array('6','4','3', '5','','')
    // );
    
    $rokudoku_hard = array (
		array('','','', '','6',''),
		array('','','', '1','',''),
		array('','','3', '','5',''),
		array('','','', '6','',''),
		array('','','6', '','',''),
		array('1','','', '','3','')
    );


	$igra = $_SESSION['igra'];
    
    //ako tek pocinje postavi pocetno stanje
    if( isset($_POST['rokudoku']) ){    
		switch( $_POST['rokudoku'] ){
			case "easy":
                $igra->inicijaliziraj( $rokudoku_easy );
				break;
			case "medium":
				$igra->inicijaliziraj( $rokudoku_medium );
				break;
			case "hard":
				$igra->inicijaliziraj( $rokudoku_hard );
				break;
		}
	}	
}
// echo '<pre>$_POST=';
// print_r($_POST);
// echo '<br>$_SESSION=';
// print_r($_SESSION);
// echo'</pre>';

$igra->run();

//kraj igre
if( $igra->je_li_gotovo() ){
	session_unset();
	session_destroy();
}
//inače ako igra još nije gotova spremi trenutno stanje u SESSION
else{
	$_SESSION['igra'] = $igra;	
}

?>