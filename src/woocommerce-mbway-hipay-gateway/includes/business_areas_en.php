<?php
global $business_areas;
$business_areas = ["2|disabled|FOOD - DRINKS",
                "3|Catering/Food",
                "4|Beers/Wines/Spirituous beverages",
                "5|Different kinds of food/gourmet",
                "6|Other beverages",
                "7|Restaurants/bars",
                "8|Supermarkets/grocery stores",
                "9|Dietary supplements",
                "10|disabled|WEAPONS",
                "11|Bladed weapons",
                "12|Civilian firearms",
                "13|False Weapons/Toys",
                "14|Historical/Collection Weapons",
                "15|Military weapons",
                "16|Hunting",
                "17|Sport shooting",
                "18|disabled|CARS - MOTORCYCLES",
                "19|Auto/motorcycle accessories",
                "20|Auto/Motorcycle Rental",
                "21|Classified Ads Auto/Motorcycles",
                "22|(22) Auto/Motorcycle Dealers",
                "23|(23) Driving schools",
                "24|(24) Motorcycles",
                "25|(25) Mechanical spare parts/Products",
                "26|(26) Auto/Motorcycle Insurance",
                "27|(27) Auto/Motorcycle Services",
                "28|disabled|(28) BUSINESS-TO-BUSINESS",
                "29|(29) Databases",
                "30|(30) Communication/B2B Advertising",
                "31|Consulting services",
                "32|Crowndfounding",
                "33|Law/Legal",
                "34|Distribution",
                "35|Training B2B",
                "36|Wholesalers B2B",
                "37|Industry",
                "38|Maintenance/Repair B2B",
                "39|Marketing",
                "40|Specialised materials B2B",
                "41|Business furniture",
                "42|Corporate services",
                "43|disabled|HOME - GARDEN - TOOLS",
                "44|Accessories/Furniture for garden",
                "45|Pets",
                "46|Do it Yourself (Bricolage)",
                "47|Kitchen",
                "48|Home/Garden Decoration",
                "49|Home Appliances",
                "50|Home/Garden Furniture",
                "51|Products/Services Home/Garden",
                "52|disabled|MUTLTIPRODUCT PURCHASES",
                "53|Warehouses/Commercial Galleries",
                "54|Shopping guide/Comparator",
                "55|Promotions/Discounts/Outlet",
                "56|disabled|SPORTS - HOBBIES - GOING OUT",
                "57|Graphic arts/craftsmanship",
                "58|Art/Collection Objects",
                "59|Touristic/cultural attractions",
                "60|Digital Content for Adults",
                "61|Events/Ticket Office",
                "62|Board games/Toys",
                "63|Games/Sweepstakes",
                "64|Sports/Leisure Bookstore",
                "65|Sports equipment/accessories",
                "66|Leisure equipment/accessories",
                "67|Music/Dance",
                "68|Clothing/footwear Sports/Leisure",
                "69|Sporting services",
                "70|Sexshops",
                "71|Vídeo/Cinema",
                "72|disabled|EDUCATION - EMPLOYMENT - TEACHING",
                "73|Extra-curricular activities",
                "74|Library",
                "75|School/Professional Certification",
                "76|Employment/Career",
                "77|Primary/secondary school",
                "78|University education",
                "79|Teaching Equipment",
                "80|Driving Schools/Teaching",
                "81|Professional training",
                "82|School Bookshop",
                "83|Learning Software",
                "84|School uniforms",
                "85|disabled|ELECTRONICS - IT",
                "86|Web Hosting Computing",
                "87|Audio/Video/Photo",
                "88|Classified ads Electronics/IT",
                "89|Electronics/computing training",
                "90|Online games",
                "91|Electrical/Electronic equipment",
                "92|Computing equipment",
                "93|IT parts/accessories",
                "94|Security/Electronic Surveillance",
                "95|Electronics/Computing Services",
                "96|Software houses",
                "97|Specific Software",
                "98|Technology",
                "99|disabled|SPIRITUALITY - OCCULT SCIENCE",
                "100|Astrology",
                "101|Esotericism/Multiservices FM",
                "102|Magic",
                "103|Magnetism/Hypnotism",
                "104|Religion/Spirituality",
                "105|Clairvoyance",
                "106|Shamanism",
                "107|disabled|FINANCE - SERVICES AND PRODUCTS",
                "108|Exchange Agencies",
                "109|Credit Agencies",
                "110|Online Stock Exchange",
                "111|Credit Cards",
                "112|Advice Finance/Investments",
                "113|Accounting",
                "114|Loans/Credits",
                "115|Debt collection agencies",
                "116|Miscellaneous insurance",
                "117|disabled|REAL ESTATE - HOUSING",
                "118|Real estate agencies",
                "119|Accommodation for holidays",
                "120|Real Estate Classified Ads",
                "121|Real Estate Training",
                "122|Auctions/Bid",
                "123|Real Estate/Accommodation",
                "124|Notary Services/Certificates",
                "125|disabled|JOGOS - CONCURSOS",
                "126|Online betting",
                "127|Browsers Games",
                "128|Casino",
                "129|Sweepstakes/Auctions",
                "130|Games",
                "131|Video games",
                "132|Poker",
                "133|disabled|FASHION - BEAUTY",
                "134|Fashion/beauty accessories",
                "135|Babys/children",
                "136|Shoes/Footwear industry",
                "137|Swimwear/Beachwear",
                "138|Fashion/beauty wholesalers",
                "139|Jewelry/Watches",
                "140|Leather goods",
                "141|Beauty care/cosmetics/perfumes",
                "142|Ready-to-wear clothes",
                "143|Underwear",
                "144|Beauty services",
                "145|Facial/body treatments",
                "146|Women's clothing",
                "147|Men's clothing",
                "148|Technical clothing/footwear",
                "149|disabled|NEWS &amp; INFORMATION - BOOKSTORE - STATIONARY",
                "150|Press/Electronic publishing",
                "151|Press/Other media",
                "152|Newspapers/Publishing",
                "153|Bookstore",
                "154|Stationery Store",
                "155|Magazines",
                "156|disabled|OCCASIONS AND EVENTS",
                "157|Catering/Events",
                "158|Design/Printing/Invitations",
                "159|Event Decoration",
                "160|Space/Equipment/Event Service",
                "161|Flowers",
                "162|Photography",
                "163|List of Gifts",
                "164|Merchandising",
                "165|Event organization",
                "166|Programs/Animation/Leisure",
                "167|disabled|PUBLIC ORGANISATIONS - ASSOCIATIONS",
                "168|Clubs/Associations/Federations",
                "169|Non-governmental organisations",
                "170|Public Service/Government",
                "171|disabled|PORTALS - SEARCH ENGINES - COMMUNITIES",
                "172|Community blogs/webservices",
                "173|Search engines",
                "174|Specific websites/portals",
                "175|General websites/portals",
                "176|Dating website",
                "177|disabled|HEALTH - FITNESS",
                "178|Dental care",
                "179|Medical Care/Therapies",
                "180|Dietetics/Nutrition",
                "181|Specialised Health Equipments",
                "182|Pharmacy",
                "183|Fitness",
                "184|Parapharmacy",
                "185|Products for Pets",
                "186|Optician services",
                "187|Veterinary Services",
                "188|Food Supplements",
                "189|disabled|TOBACCO",
                "190|Tobacco Accessories",
                "191|Cigars",
                "192|Cigarettes",
                "193|Electr Cigarettes (w/nicotine)",
                "194|Electr Cigarettes (w/ nicotine)",
                "195|Multiproducts/Tobacco",
                "196|Tobacco",
                "197|disabled|TELECOM - INTERNET SERVICES",
                "198|Web Hosting Telecommunications",
                "199|Maintenance/ Repair Internet",
                "200|Telecommunications equipment",
                "201|Security/Surveillance online",
                "202|Telecommunications services",
                "203|Fixed Phone/VOIP",
                "204|Telephone/Mobile Services",
                "205|disabled|TRAVELLING AND WELL-BEING",
                "206|Travel Agencies",
                "207|Luggage",
                "208|Blogs/Online Information",
                "209|Touristic circuits",
                "210|Cruises",
                "211|Entertainment",
                "212|Hotels/Accommodation facilities",
                "213|Travel Bookstore",
                "214|Passports/Other Documents",
                "215|SPA Centres/Institutes",
                "216|Transportation",
                "217|Tourism",
                "218|Entry visas"];