class AllRecipesComponent extends Fronty.ModelComponent {
    constructor(postsModel, userModel, router) {
        super(Handlebars.templates.allrecipes, postsModel, null, null);


        this.postsModel = postsModel;
        this.userModel = userModel;
        this.addModel('user', userModel);
        this.router = router;

        this.postsService = new PostsService();

        this.ingredientsModel = new IngredientsModel();
        this.addModel('ingredients', this.ingredientsModel);

        var postsService = new PostsService();

        postsService.findAllIngredients().then((data) => {
            this.ingredientsModel.setIngredients(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new IngredientModel(item.name)
                ));
        });

        this.addEventListener('click', '#filterbutton', () => {
            var checkboxes = document.getElementsByName("ingredientFilter");
            var checkboxesChecked = [];
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    checkboxesChecked.push(checkboxes[i].value);
                }
            }
            //console.log(checkboxesChecked);
            this.updatePostsFilters(checkboxesChecked);


        });
    
    }

    onStart() {
        this.updatePosts();
    }

    updatePosts() {
        this.postsService.findAllRecipes().then((data) => {

            this.postsModel.setPosts(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new PostModel(item.id, item.title, item.content, item.author, item.time, item.date, item.image,null,null,null,item.like,item.count)
                ));
        });
    }

    updatePostsFilters(checkboxesChecked) {
        //console.log(checkboxesChecked);
        this.postsService.filters(checkboxesChecked).then((data) => {

            this.postsModel.setPosts(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new PostModel(item.id, item.title, item.content, item.author, item.time, item.date, item.image,null,null,null,null,item.count)
                ));
        });
    }


    // Override
    createChildModelComponent(className, element, id, modelItem) {
        return new PostRowComponent(modelItem, this.userModel, this.router, this);
    }



}