$(function(){
    $(".quantity").on('click', function (e) {
        e.stopPropagation();
        orderQuantityAction(e, $(this).find("input[name=quantity]"));
        return false;
    });
});

function orderQuantityAction (e, input) {
    var target = e.target;
    var value = ~~input.val();
    if (target.dataset.actionCount === "inc") {
        value++;
    } else if (target.dataset.actionCount === "dec") {
        value--;
    }
    input.val(Math.max(0, value));
}