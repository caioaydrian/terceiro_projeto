<main>
    <section>
        <h1>Nosso Menu</h1>

        <section class="categoria" id="categoria-bebidas">
            <h2>Categoria Nome</h2>
            <ul class="lista-bebidas">
                <li class="bebida">
                    <span class="nome">bebida nome</span>
                    <span class="preco">bebida preco</span>
                </li>
            </ul>
        </section>

    </section>
</main>

<script src="dist/menu.js"></script>




<!-- ANTIGO -->

<!-- <main>
    <section>
        <h1>Nosso Menu</h1>

        <?php if (!empty($lista_cafeteria)): ?>
            <?php
            $por_categoria = [];
            foreach ($lista_cafeteria as $linha) {
                $cat = $linha['categoria'] ?? 'Sem Categoria';
                $por_categoria[$cat][] = $linha;
            }
            ?>

            <?php foreach ($por_categoria as $cat_nome => $itens): ?>
                <section class="categoria">
                    <h2><?php echo htmlspecialchars($cat_nome); ?></h2>
                    <ul class="lista-bebidas">
                        <?php foreach ($itens as $item): ?>
                            <li class="bebida">
                                <span class="nome"><?php echo htmlspecialchars($item['bebida']); ?></span>
                                <span class="preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Sem itens no menu no momento.</p>
        <?php endif; ?>

    </section>
</main> -->