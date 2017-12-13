var Content = {};
Content.Manager = (function(){
    return{
        topMenuLendingWallet: function(priceValue){
            $('.lending_wallet_balance_dollar').text(priceValue);
        },
        totalAndActiveInvestment: function total_investment(priceSearch, priceValueInvestment) {
            var repeatSearch = true;
            setTimeout(function(){

                $('b').each(function () {

                    var contents = $(this).contents();

                    if (typeof contents[4] != 'undefined') {
                        var text = $(this).text();
                        if (text.search(priceSearch) > -1) {

                            var valText = text.replace(priceSearch, priceValueInvestment);
                            var valueDollars = valText.substr(valText.length - 10);
                            contents[4].nodeValue = valueDollars;
                            repeatSearch = false;
                        }
                    }
                });

                if(repeatSearch){
                    total_investment(priceSearch, priceValueInvestment);
                }

            }, 1);

        },
        totalEarns: function total_earns(priceSearch, priceSwitch) {
            var repeatSearch = true;
            setTimeout(function(){

                $('th').each(function () {

                    var contents = $(this).contents();

                    if (typeof contents[1] != 'undefined') {
                        var text = $(this).text();
                        if (text.search(priceSearch) > -1) {

                            var valText = text.replace(priceSearch, priceSwitch);
                            var valueDollars = valText.substr(valText.length - 8);
                            contents[1].nodeValue = valueDollars;
                            repeatSearch = false;
                        }
                    }
                });

                if(repeatSearch){
                    total_earns(priceSearch, priceSwitch);
                }

            }, 1);

        }

    };
})();

var totalLendingWallet = "174.26";

var totalActiveSearchVal = "1,280.00";
var totalActiveTargetVal = "11,935.87";

var totalEarnsSearchVal = "226.45";
var totalEarnsTargetVal = "4,038.52";

Content.Manager.topMenuLendingWallet(totalLendingWallet);

$("#total_investment").click(function(){
    Content.Manager.totalAndActiveInvestment(totalActiveSearchVal, totalActiveTargetVal);
});

$("#total_active_investment").click(function(){
    Content.Manager.totalAndActiveInvestment(totalActiveSearchVal, totalActiveTargetVal);
});

$("#total_earned_anchor").click(function(){
    Content.Manager.totalEarns(totalEarnsSearchVal, totalEarnsTargetVal);
});







