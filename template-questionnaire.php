<?php
/*
* Template Name: Questionnaire
*/

get_header();

class Question
{
    public $question;
    public $answers;
    public $value;
}

$question_list = [];
$answers_list = [];

if (have_rows('questions')) :
    while (have_rows('questions')) : the_row();
        $question_object = new Question();
        $question_object->question = get_sub_field('question');
        if (have_rows('reponses')) :
            while (have_rows('reponses')) : the_row();
                $question_object->answers[] = get_sub_field('reponse');
                $question_object->value[] = get_sub_field('valeur');
            endwhile;
        endif;
        $question_list[] = $question_object;
    endwhile;

endif;

?>

<main id="questionnaire">
    <section class="hero-banner">
        <div class="leaf">
            <img src="<?= get_template_directory_uri(); ?>/img/top-leaf.svg" alt="Feuille de vigne">
        </div>
        <div class="container">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </div>
    </section>
    <article>
        <div class="container">
            <h2><?php the_field('titre_grandes_familles'); ?></h2>
            <?php if (have_rows('les_familles')) :
                $i = 0;
            ?>
                <div class="familles-btn">
                    <?php while (have_rows('les_familles')) : the_row(); ?>
                        <button class="btn btn-primary" id="<?= $i; ?>"><?= get_sub_field('texte_bouton'); ?></button>
                        <?php $i++; ?>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <?php if (have_rows('les_familles')) :
                $j = 0;
            ?>
                <div class="familles">
                    <?php while (have_rows('les_familles')) : the_row(); ?>
                        <div class="famille" id="<?= $j ?>">
                            <?php $image_famille = get_sub_field('image'); ?>
                            <img src="<?= esc_url($image_famille['url']); ?>" alt="<?= esc_attr($image_famille['alt']); ?>">
                            <p><?php the_sub_field('texte'); ?></p>
                        </div>
                    <?php $j++;
                    endwhile; ?>
                </div>
            <?php endif; ?>
            <div class="questionnaire-content">
                <?php the_field('contenu'); ?>
            </div>
        </div>
    </article>
    <template v-if="!isLoading">
        <section class="question-part">
            <div class="container">
                <h2>Questionnaire</h2>
                <div class="questions-container">
                    <div class="question" v-for="question in data.slice(sliceA, sliceB)">
                        <h3>{{ question.question  }}</h3>
                        <form action="" class="choices">
                            <div class="choice" v-for="(answer, index) in question.answers">
                                <input type="radio" :id="question.value[index]" :name="question.question" :value="question.value[index]" v-model="result">
                                <label :for="question.value[index]">{{ answer }}</label>
                            </div>
                        </form>
                    </div>
                    <button class="validate" @click="handleClick()">Valider</button>
                </div>
            </div>
        </section>
    </template>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    data: null,
                    sliceA: 0,
                    sliceB: 1,
                    step: 1,
                    isLoading: true,
                    result: null,
                    results: [],
                }
            },
            async mounted() {
                await this.getData();
                this.isLoading = false;
            },
            methods: {
                getData() {
                    let options = {
                        method: 'GET',
                        url: 'https://leshop-chezolympe.btg-dev.com/api',
                        params: {
                            output_format: 'JSON'
                        },
                        headers: {
                            cookie: 'PrestaShop-754200b4a6d6439be364664671f88aa3=def502003afe9362afac3b400d9af117856c8e117d7c3eaf133a4cf9be2166b42750dca255f1ff86425329c72a5c5cded545ea067f1625a8e9051eab090151a23645f269da7ecc07974cee9fb3d74c59109659b0735e2f37f88142d82568addc5d934775dceab6ec3fc6e54b8515359ff765c73ee955e3ea56ed080bd141a7e67520a0d7eff5fb135e20924e04d80b857362cdd59865aedf995300ceed41cfa0bb70660da8c0c541d514c901f78d990b798a1a2cec9d49afc6f8beed546314181fb01cde68786eac55677f13caeeecab171ef800546b6c5c39',
                            Authorization: `Basic WR19W8DUK6YKMJGYZU9UGHI8TDYWBJM3`
                        }
                    };
                    this.data = <?= json_encode($question_list); ?>;
                    axios.request(options).then(data => {
                        console.log(data)
                    }).catch(err => {
                        console.log(err)
                    })
                },
                handleClick() {
                    this.results.push(this.result);
                    this.result = null;
                    if (this.sliceB < this.data.length) {
                        this.sliceA = this.sliceA + this.step;
                        this.sliceB = this.sliceB + this.step;
                    }
                }
            }
        }).mount('#questionnaire');
    </script>
</main>


<?php
get_template_part('parts/bottom');
get_footer();
?>