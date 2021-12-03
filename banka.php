<?php
if(!isset($_GET['id'])){
    header('Location: index.html');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Banka</title>
</head>

<body>

    <div class="container mt-5">
        <div class="row">
            <div class="col-2">
                <a href="index.html">
                    <button class="btn btn-secondary form-control">Nazad</button>
                </a>
            </div>
            <div class="col-8">

            </div>
            <div class="col-2">
                <button id='obrisiBanku' class="btn btn-danger form-control">Obrisi</button>
            </div>
        </div>
        <h1 class="text-center">
            Izmeni banku
        </h1>
        <div class="mt-2">
            <form id='formaBanka'>
                <label>Id</label>
                <input type="text" class="form-control" disabled id='idBanke' value="<?php echo $_GET['id']; ?>">
                <label>Naziv banke</label>
                <input id="naziv" class="form-control" />
                <label>Prefiks racuna</label>
                <input id="prefiks" class="form-control" />
                <label>Sediste</label>
                <input id="sediste" class="form-control" />
                <label>Tip banke</label>
                <select id="tip" class="form-control"></select>
                <button class="btn btn-primary mt-2 form-control" type="submit">Izmeni</button>
            </form>
        </div>
        <div class="mt-2">
            <h2 class="text-center">
                Ekspoziture banke
            </h2>
        </div>
        <div class="row mt-2">
            <div class="col-6">
                <table class="table table striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Adresa</th>
                            <th>Broj telefona</th>
                            <th>Obrisi</th>
                        </tr>
                    </thead>
                    <tbody id='ekspoziture'>

                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <h3 class="text-center">
                    Kreiraj ekspozituru
                </h3>
                <form id='eksForma'>
                    <label>Adresa</label>
                    <input id="adresa" class="form-control" />
                    <label>Broj telefona</label>
                    <input id="telefon" class="form-control" />
                    <button class="btn btn-primary mt-2 form-control" type="submit">Kreiraj</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        let banka = undefined;
        let id = 0;
        $(document).ready(function () {

            $('#eksForma').submit(function (e) {
                e.preventDefault();
                const adresa = $('#adresa').val();
                const brojTelefona = $('#telefon').val();
                $.post('server/index.php?akcija=ekspozitura.create&bankaId=' + id, {
                    adresa,
                    brojTelefona
                }, function (res) {
                    res = JSON.parse(res);
                    if (!res.status) {
                        alert(res.error);
                    } else {
                        alert("Uspesno dodata ekspozitura")
                    }
                    ucitajBanku();
                })
            })
            $('#obrisiBanku').click(function () {
                $.post('server/index.php?akcija=banka.delete&id=' + id, function (res) {
                    res = JSON.parse(res);
                    if (!res.status) {
                        alert(res.error);
                    } else {
                        alert('Uspesno obrisana banka');
                        window.location = 'index.html';
                    }
                })
            })
            $('#formaBanka').submit(function (e) {
                e.preventDefault();
                const naziv = $('#naziv').val();
                const prefiks = $('#prefiks').val();
                const sediste = $('#sediste').val();
                const tip = $('#tip').val();
                $.post('server/index.php?akcija=banka.update&id=' + id, {
                    naziv,
                    prefiks,
                    sediste,
                    tipId: tip
                }, function (res) {
                    res = JSON.parse(res);
                    if (!res.status) {
                        alert(res.error);
                    } else {
                        alert("Uspesno izmenjena banka")
                        ucitajBanku();
                    }
                })
            })
            id = Number($('#idBanke').val());
            $.getJSON('server/index.php?akcija=tip.read', function (res) {
                if (!res.status) {
                    alert(res.error);
                    return;
                }
                for (let tip of res.data) {
                    $('#tip').append(`
                        <option value='${tip.id}'>
                            ${tip.naziv}
                        </option>
                    `)
                }
            })
            ucitajBanku();
        })
        function ucitajBanku() {
            $.getJSON('server/index.php?akcija=banka.one&id=' + id, function (res) {
                if (!res.status) {
                    alert(res.error);
                    window.location = 'index.html';
                    return;
                }
                banka = res.data;
                popuniHtml();
            })
        }
        function popuniHtml() {
            $('#naziv').val(banka.naziv);
            $('#sediste').val(banka.sediste);
            $('#prefiks').val(banka.racunPrefiks);
            $('#tip').val(banka.tip.id);
            $('#ekspoziture').html('');
            for (let eks of banka.ekspoziture) {
                $('#ekspoziture').append(`
                    <tr>
                        <td>${eks.id}</td>
                        <td>${eks.adresa}</td>
                        <td>${eks.brojTelefona}</td>
                        <td>
                            <button class='btn btn-danger form-control' onClick="obrisiEkspozituru(${eks.id})">Obrisi</button>
                        </td>
                    </tr>
                `)
            }
        }
        function obrisiEkspozituru(eId) {

            $.post(`server/index.php?akcija=ekspozitura.delete&id=${eId}&bankaId=${id}`, function (res) {
                res = JSON.parse(res);
                if (!res.status) {
                    alert(res.error);
                } else {
                    alert("Uspesno obrisana ekspozitura")
                }
                ucitajBanku();
            })
        }
    </script>
</body>

</html>