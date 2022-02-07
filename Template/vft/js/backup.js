const categoryDescriptionContainer = document.querySelector('.shop-menu__category-desc');
if (categoryDescriptionContainer != null) {
    let currentPageUri = document.location.pathname;
    switch (currentPageUri) {
        case '/shop/industry-food-supplements/':
            categoryDescriptionContainer.innerHTML = '<h2>Food Supplements</h2><p>Due to unique characteristics of siberian mushrooms and herbs, VitaForest\’s extracts and powders are used for production of active food supplements for already more than 5 years. They help to endure rugged environment and increase human body\’s adaptation mechanisms.</p> <p>Handpicking of mushrooms and herbs in outlying Siberian areas, gentle processing and scientifically proven adjusted to each ingredient production technology give an opportunity to keep unique bioactive compounds in VitaForest\'s powders and extracts. Food supplements produced with siberian mushrooms and herbs may: </p> <ul> <li>help to endure rugged environment;</li> <li>regulates sleep patterns;</li> <li>increase libido;</li> <li>increase stress-resistance;</li> <li>decrease chronical fatigue;</li> <li>clear of apathy, inertness, constant urge to sleep;</li> <li>ease jet lag, effects of climate change and new schedule adjustments struggles;</li> <li>reduce effect of harmful aspects at production.</li> </ul> <p>VitaForest\'s extracts and powders from siberian mushrooms and herbs give an opportunity of production highly competitive and ecological friendly food supplements with high concentration of polysaccharides, glycosides, phytosterols, flavonoids and terpenoids. Their action is aimed at improving the well-being of a human body and solving local health problems. </p>';
            break;
        case '/shop/industry-cosmetics/':
            categoryDescriptionContainer.innerHTML = '<h2>Cosmetics</h2><p>Cosmetic producers highly value VitaForest\’s herbal, berry, fruit and mushroom powders for the fantastic characteristics of siberian areas. In nature, there are a huge amount of plants, berries, fruits and mushrooms which proved to be efficient during external application. However, it is Siberian nature that is deservedly considered a storehouse of the most valuable active components, which is confirmed by numerous scientific studies.</p> <p>Rhodiola rosea of Russian origin is added into creams and lotions as an effective agent for delaying aging due to high concentration of total rosavins (rosavin, rosarin, rosin) and salidroside in it. Siberian eleutherococcus contains a wide range of minerals which can be found only in that type of ginseng. It is used in cosmetic for skin care purposes. Nanain citronella hips are rich of E and C vitamins, minerals and essential oils and are natural antioxidants. Manchurian aralia rejuvenates, strengthens skin, improves it\’s protection from external influences and recovers it elasticity and smoothes wrinkles.</p> <p>In cosmetic production Rhodiola rosea, chaga (Inonotus obliquus), milk thistle (Silybum marianum), purple coneflower (Echinacea purpurea), siberian ginseng (Eleutherococcus senticosus), Baikal skullcap (Scutellaria baicalensis) and other siberian plant, berry, fruit and mushroom powders and extracts are used.</p>';
            break;
        case '/shop/industry-pharmaceutical-industry/':
            categoryDescriptionContainer.innerHTML = '<h2>Pharmaceuticals</h2><p>VitaForest\’s medicinal herbs, berries, fruits and mushrooms extracts and powders are used in production of anti-inflammatory, repairing, immunomodulatory and preventive care pastes, gels, suppositories and hygienic agents. It is driven by fact of high concentration of bioactive substances in Siberian plant, berry, fruit and mushroom sources and their unique characteristics. They are able to increase the body\'s resistance to various infections, promote wound healing and prevent the development of inflammatory processes. </p> <p>In pharmaceutical industry most commonly such dry powders and extracts as Rhodiola rosea, rose hip (Rosa spp.), astragalus (Astragalus spp.), hawthorn (Crataegus spp.), St. John\'s wort (Hypericum perforatum), elderberry (Sambucus nigra), stinging nettle (Urtica dioica), Siberian ginseng (Eleutherococcus senticosus) and other herbs, berries, fruits and mushrooms are used.</p> <p>We place greater focus on our product and we conduct quality tests on different stages of production. To start with, verification of herbs during foraging stage, qualitative and quantitative analysis of raw materials, and qualitative analysis of ready powder or extract. Sigma-Aldrich`s matter standards are used during laboratory tests implementation. Ready products comply with EU safety and quality standards.</p> <p>Upon your request, we can develop technical documentation and produce natural dry extracts and powders from any wild healthful siberian sources.</p>';
            break;
        case '/shop/industry-healthy-nutrition/':
            categoryDescriptionContainer.innerHTML = '<h2>Healthy nutrition</h2><p>Producers of sport, healthy, diabetica land other specialized food and beverage in numerous countries already acknowledged benefits of functional VitaForest extracts and powders, produced from Siberian berries, fruits, plants and mushrooms. Their uniqueness is manifests in the capability of improving general well-being and in high concentration of bioactive substances such as flavonoids, glycosides, polysaccharides, phytosterols, terpenoids and etc.  </p> <p>Application of extracts and powders made from Siberian berries, fruits, mushrooms and plants, including adaptogens, in production contributes to establishing highly competitive healthy food products lines which:</p> <ul> <li>increase physical and mental capacity, without negative impact on human body;</li> <li>support cognitive functions such as memory, thinking and focus abilities;</li> <li>delay aging;</li> <li>increase vitality;</li> <li>boost immunity;</li> <li>prevents tumor growth;</li> <li>increase durability during physical exercises and sport trainings.</li> </ul> <p>The most valuable properties relate to dry extracts and powders of Rhodiola rosea, Siberian ginseng (Eleutherococcus senticosus), maral root (Rhaponticum carthamoides), ginseng (Panax ginseng), schizandra (Schisandra chinensis) and Manchurian aralia (Aralia elata), black chokeberry (Aronia melanocarpa) are the richest with useful qualities</p> <p>The dry form of VitaForest extracts is convenient in use in the production of dietary marmalade, cookies, nutritional bars and snacks, muesli, cocktails, tea, coffee, fruit water infusions and other functional drinks.</p>';
            break;
        case '/shop/industry-feed-additives/':
            categoryDescriptionContainer.innerHTML = '<h2>Feed additives</h2><p>VitaForest\’s functional extracts and powders made out of siberian wild herbs, berries, fruits, roots and mushrooms are used in veterinary medicine, dry pet food production and feed additives for farms and aquacultures. </p> <p>Siberian birch chaga is a natural energy source for animals. Chaga extracts and powders are used in veterinary practice and animal farming as a natural growth stimulant for juveniles. As researchers have proved, chaga positively affects blood and protein forming liver\’s function, and, in addition, helps in curing indigestion among young animals and increases haemoglobin levels. </p> <p>Extracts and powders such medicinal herbs, berries, fruits and mushrooms as Rhodiola rosea, common dandelion (Taraxacum officinale), purple coneflower (Echinacea purpurea), baikal skullcap (Scutellaria baicalensis), common melilot (Melilotus officinalis) and others are used as animal feed supplement, bioactive ingredients for veterinary pastes, suppositories and other drugs, and also can be used as a flavoring and aromatic additive for the production of animal feeds. </p> <p>Siberian mushrooms, berries, fruits, roots and herbs are famous for their adaptogenic characteristics. Regular application positively affects growth, development, health and appearance of pets and animals.</p>';
            break;
        case '/shop/industry-food-and-beverages/':
            categoryDescriptionContainer.innerHTML = '<h2>Food and beverages</h2><p>Due to the high content of biologically active substances, VitaForest dry functional extracts and powders are often used in confectionery, meat, dairy and other industries of new generation food and beverages - useful, stimulating various functions and systems of the human body. </p> <p>Dry extracts are used as flavoring additives to improve the organoleptic properties of alcoholic and low-alcoholic beverages. For example, oak (Quercus spp.), wormwood (Artemisia absinthium), willow (Salix spp.), thyme (Thymus spp.), elecampane (Inula helenium), tansy flower (Tanacetum vulgare) and yarrow (Achillea millefolium) plant extracts are used to impart natural bitterness in the production of vodkas, tinctures and vermouths. </p> <p>Chaga extract and powder are used in the production of cold tea, dairy and dairy-free drinks based on rice or almonds. Chaga gained a reputation of a perfect coffee substitute thanks to its rich flavor or an additive to it due to its ability to neutralize organic acids of coffee beans that irritate the stomach lining. </p> <p>Other most popular extracts and powders in the food and beverage industries are made of oregano (Origanum vulgare), hop (Humulus lupulus), maral root (Rhaponticum carthamoides), schisandra (Schisandra chinensis), Rhodiola rosea, rose hip (Rosa spp.), hawthorn (Crataegus spp.), raspberry (Rubus idaeus), wild strawberry (Fragaria vesca), grape (Vitis vinifera) and others. Addition of them into food and beverage products brings in unique, memorable and easily recognizable charm among many competitors.</p>';
            break;
        default:
            console.log('Миша, всё хуйня, давай по новой.');
    }
}