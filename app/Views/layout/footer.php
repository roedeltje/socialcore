</main>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold mb-2">SocialCore</h3>
                    <p class="text-gray-400">Een open source sociaal netwerkplatform</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-2">Links</h3>
                    <ul class="text-gray-400">
                        <li><a href="<?= base_url('index.php?route=over') ?>" class="hover:text-white">Over ons</a></li>
                        <li><a href="#" class="hover:text-white">Privacy</a></li>
                        <li><a href="#" class="hover:text-white">Voorwaarden</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-700 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> SocialCore Project. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>
</body>
</html>