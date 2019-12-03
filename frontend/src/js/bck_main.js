$('document').ready(function() {

    var alphabetIndex = [];
    initApp();


    function initApp() {

        let alphabet = "abcdefghijklmnopqrstuvwxyz";
        for (i in alphabet) {
            let letter = alphabet[i];
            alphabetIndex[letter] = i;
            //console.log('lettera ' + alphabet[i] + ': posizione ' + i);
        }
        countCharInput();
        alphabetNumberInput();


        let currentdate = new Date();
        let nowtime = "Last Sync: " + currentdate.getDay() + "/" + currentdate.getMonth() +
            "/" + currentdate.getFullYear() + " @ " +
            currentdate.getHours() + ":" +
            currentdate.getMinutes() + ":" + currentdate.getSeconds();

        $('#nowtime span').text(nowtime);
    }


    function countCharInput() {

        countchar = $('#domain1').val().length + $('#domain2').val().length;
        $('#countInputChar span').text(countchar);

    }

    function alphabetNumberInput() {

        let input = $('#domain1').val().trim().toLowerCase();
        let sumInput = 0;

        for (i in input) {
            let letter = input[i];
            if (typeof alphabetIndex[input[i]] != "undefined") {
                sumInput += parseInt(alphabetIndex[input[i]]) + 1;
                //console.log('lettera ' + input[i] + ': posizione ' + i + ' numer ' + alphabetIndex[input[i]]);
            }

        }
        $('#alphabetNumberInput span').text(sumInput);

    }

    function downloadFileCsv2(urlPage) {

        // Create an invisible A element
        const a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);

        // Set the HREF to a Blob representation of the data to be downloaded
        a.href = urlPage;
        a.setAttribute("target", "_blank");
        // Trigger the download by simulating click

        /*a.addEventListener("click", function(event) {
            event.preventDefault()
            
        });*/
        a.click();
        // Cleanup
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);

    }

    function downloadFileCsv(urlPage) {

        var url = urlPage;
        wd = window.open(url, 'Download');
        // wd.close();

    }


    function validateDomain(strUrl) {

        if (check_url(strUrl) == false)
            return false;

        url = new URL(strUrl);

        protocol = url.protocol;
        hostname = url.hostname;
        port = url.port;

        if (protocol != 'https:' && protocol != 'http:')
            return false;

        valido = protocol + '//' + hostname;
        if (port != '')
            valido += ':' + port;

        if (valido == strUrl || valido + '/' == strUrl)
            return true;
        else
            return false;

    }

    function check_url(str) {

        var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
            '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator 

        return !!pattern.test(str);

    }


    $('form').on('keyup', 'input', function(ele) {

        $('#domain1').val($('#domain1').val().trim());
        $('#domain2').val($('#domain2').val().trim());
        countCharInput();
        alphabetNumberInput();

    });

    $('body').on("submit", "form", function(event) {

        event.preventDefault();
        domain1 = $('#domain1').val().trim();
        domain2 = $('#domain2').val().trim();

        if (domain1 == '' || domain2 == '') {
            alert("Verificare i valori immessi");
            return false;
        }

        if (domain1 == domain2) {
            alert('inserire domini diversi');
            return false;
        }

        if (validateDomain(domain1) == false || validateDomain(domain2) == false) {
            alert("verificare i valori immessi");
            return false;
        }


        endpointUrlCrawl = appConfig.endpointUrlCrawl;
        endpointUrlCrawl += '?domain1=' + domain1 + '&domain2=' + domain2;
        console.log(appConfig.endpointUrlCrawl);
        downloadFileCsv(endpointUrlCrawl);
        return false;
        $.ajax(
            endpointUrlCrawl, {
                method: 'GET',
                data: {
                    'domain1': encodeURIComponent(domain1),
                    'domain2': encodeURIComponent(domain2)
                },
                complete: function(resp) {
                    alert(resp.responseText);

                }
            }
        );

    });


});