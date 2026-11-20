(function () {
    var search = document.getElementById("pet-search");
    var statusFilter = document.getElementById("status-filter");
    var cards = Array.prototype.slice.call(document.querySelectorAll(".pet-card"));
    var noMatches = document.getElementById("no-matches");
    var count = document.getElementById("pet-count");

    document.querySelectorAll("[data-pet-photo]").forEach(function (image) {
        image.addEventListener("error", function () {
            image.hidden = true;
            image.nextElementSibling.hidden = false;
        });
    });

    if (!search || !statusFilter || !noMatches || !count) {
        return;
    }

    function updatePets() {
        var query = search.value.trim().toLocaleLowerCase();
        var selectedStatus = statusFilter.value.toLocaleLowerCase();
        var visibleCount = 0;

        cards.forEach(function (card) {
            var matchesSearch = card.dataset.search.toLocaleLowerCase().includes(query);
            var matchesStatus = !selectedStatus || card.dataset.status.toLocaleLowerCase() === selectedStatus;
            var isVisible = matchesSearch && matchesStatus;
            card.hidden = !isVisible;
            if (isVisible) {
                visibleCount += 1;
            }
        });

        count.textContent = visibleCount + (visibleCount === 1 ? " pet" : " pets");
        noMatches.hidden = visibleCount !== 0;
    }

    search.addEventListener("input", updatePets);
    statusFilter.addEventListener("change", updatePets);
})();
