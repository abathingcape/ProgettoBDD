<div class='dashboard'>
<script>
$(document).ready(function(){                  //barra di ricerca     
$('#search').on('keyup', function(){           // prende il valore della input search e lo manda con la GET al file php per fare la ricerca 
        var query = $(this).val();
        if (query.length>0){
          $(".listarisposte").show();
          $.ajax({
              url: 'ricercaheader.php',
              method: 'POST',
              data: {query: query},
              success: function(response){
                  $('.listarisposte').html(response);       // mostra la losta delle risponde 
              }
          });
        }else{
          $(".listarisposte").hide();             //nasconde la lista risposte in caso non ci sia nessun risultato 
        }
    });
    $(document).on('click', function(event){                //per far si che quando clicco fuori dalla barra di ricerca scompaia
        if(!event.target.closest('.listarisposte')){        //tranne per il caso che io stia selezionando un elemento della lista
        $(".listarisposte").hide();                        
        $("#search").val("");
  }
});
});
</script>
  <div class="dashboard-nav">

    <nav class="menu" id="menu">
      <ul class="blocco">
        <div class="pagine">
          <li class="hcbp"><i class="fa-solid fa-house"></i><a href="index.html.php" class="menu_items"> Home </a></li>
        </div>
        <div class="pagine">
          <li class="hcbp"><i class="fa-solid fa-bars"></i><a  href="listacategorie.html.php" class="menu_items"> Categorie</a></li>
        </div>
        <div class="pagine">
          <li class="hcbp"><i class="fa-solid fa-ellipsis-vertical"></i><a href="listablog.html.php" class="menu_items"> Blog </a></li>
        </div>
        <?php if ($_SESSION["premium"]==true){                              // se l'utente è premium mostrerà il tasto per accedere alla pagina dei post salvati
        echo '<div class="pagine">
          <li class="hcbp"><i class="fa-regular fa-bookmark"></i><a href="postsalvati.html.php" class="menu_items"> Post salvati </a></li>
        </div>';}?>        
      </ul>
    </nav>  
    <nav class="dashboard-nav-list" id="profilologout">
        <div class="tasto_profilo">
          <i class="fa-regular fa-user" style="margin: auto;"></i>
          <?php echo '<a href="profilo.html.php?id='. $_SESSION["user"]. '"';?>class="dashboard-nav-item"> Profilo </a>   <!--collegamento alla pagina personale dell'utente loggato-->
        </div>
        <div class="nav-item-divider"></div>
        <div class="tasto_logout">
          <i class="fa-solid fa-right-from-bracket" style="margin: auto;" id="icona_logout"></i>
          <a href="logout.php" class="dashboard-nav-item" id="pulsantelogout"> Logout </a>
        </div>

      </nav>
  </div>
  
  <div class='dashboard-app'>
      <header class='dashboard-toolbar'>
        
        <div class="grid_header">
          
          <div class="grid_item item1">
            <a href="index.html.php" class="menu-toggle" style="width: 100%; height: 80px;">
              <img src="logoBIP/logo_bianco3.svg" alt="logo_header" class="logo_header">
            </a>
          </div>
          
          <div class="grid_item item2">
              <button class="scrivipost">
                <img src="icone/scrivipost.png" alt="Scrivi post" class="iconetasti">
                <a href="scrivipost.html.php" class="tastobarra"> Scrivi post </a>
              </button>
              <button class="creablog" onclick="window.location.href='creablog.html.php'">
                <img src="icone/creablog.png" alt="Crea blog" class="iconetasti2">
                <a href="creablog.html.php" class="tastobarra"> Crea blog </a>
              </button>
              <button class="creaCat" onclick="window.location.href='creacategorie.html.php'">
                <i class="fa-solid fa-bars" style="color: #1d1e2f;"></i>
                <a href="creacategorie.html.php" class="tastobarra"> Crea Categoria </a>
              </button>
          </div>

          <div class="grid_item item3">
            <div id="wrap">
              <input id="search" name="search" type="text" placeholder="Cerca...">
              <ul class="listarisposte" hidden></ul>
              <input id="search_submit" value="Rechercher" type="submit">
            </div>
          </div>
           
        </div>
        
      </header>